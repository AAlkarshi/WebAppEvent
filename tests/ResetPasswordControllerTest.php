<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/* TEST généré automatiquement pour vérifier que le processus fonctionne */
class ResetPasswordControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Ensure we have a clean database
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $this->em = $em;

        $this->userRepository = $container->get(UserRepository::class);

        // ✅ Nettoyer la base de données dans le bon ordre
        $this->cleanDatabase();
    }

    /**
     * Nettoie la base de données en respectant les contraintes FK
     */
    private function cleanDatabase(): void
    {
        $connection = $this->em->getConnection();
        
        try {
            // Méthode rapide : désactiver les FK checks
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
            
            // Vider toutes les tables
            $tables = ['event', 'reset_password_request', 'category', 'user'];
            foreach ($tables as $table) {
                try {
                    $connection->executeStatement("TRUNCATE TABLE `$table`");
                } catch (\Exception $e) {
                    // Table n'existe peut-être pas encore
                }
            }
            
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
            
        } catch (\Exception $e) {
            // Méthode lente : supprimer dans le bon ordre avec DQL
            try {
                $this->em->createQuery('DELETE FROM App\Entity\Event')->execute();
            } catch (\Exception $e) {}
            
            try {
                $this->em->createQuery('DELETE FROM App\Entity\ResetPasswordRequest')->execute();
            } catch (\Exception $e) {}
            
            try {
                $this->em->createQuery('DELETE FROM App\Entity\Category')->execute();
            } catch (\Exception $e) {}
            
            try {
                $this->em->createQuery('DELETE FROM App\Entity\User')->execute();
            } catch (\Exception $e) {}
        }
        
        $this->em->clear();
    }

    public function testResetPasswordController(): void
    {
        // Create a test user
        $user = new User();
        $user->setMailUser('me@example.com'); // ← Utilise setMailUser() au lieu de setEmail()
        $user->setPasswordUser('a-test-password-that-will-be-changed-later'); // ← Utilise setPasswordUser()
        $user->setLastnameUser('Test');
        $user->setFirstnameUser('User');
        $user->setGenderUser(\App\Enum\GenderUser::Homme); // ou Female selon votre enum
        $user->setDatebirthUser(new \DateTimeImmutable('1990-01-01'));
        $user->setCityUser('Test City');
        
        $this->em->persist($user);
        $this->em->flush();

        // Test Request reset password page
        $this->client->request('GET', '/reset-password');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Reset your password');

        // Submit the reset password form and test email message is queued / sent
        $this->client->submitForm('Send password reset email', [
            'reset_password_request_form[email]' => 'me@example.com',
        ]);

        // Ensure the reset password email was sent
        // Use either assertQueuedEmailCount() || assertEmailCount() depending on your mailer setup
        // self::assertQueuedEmailCount(1);
        self::assertEmailCount(1);

        self::assertCount(1, $messages = $this->getMailerMessages());

        self::assertEmailAddressContains($messages[0], 'from', 'alkarshi.abdullrahman@gmail.com');
        self::assertEmailAddressContains($messages[0], 'to', 'me@example.com');
        self::assertEmailTextBodyContains($messages[0], 'This link will expire in 1 hour.');

        self::assertResponseRedirects('/reset-password/check-email');

        // Test check email landing page shows correct "expires at" time
        $crawler = $this->client->followRedirect();

        self::assertPageTitleContains('Password Reset Email Sent');
        self::assertStringContainsString('This link will expire in 1 hour', $crawler->html());

        // Test the link sent in the email is valid
        $email = $messages[0]->toString();
        preg_match('#(/reset-password/reset/[a-zA-Z0-9]+)#', $email, $resetLink);

        $this->client->request('GET', $resetLink[1]);

        self::assertResponseRedirects('/reset-password/reset');

        $this->client->followRedirect();

        // Test we can set a new password
        $this->client->submitForm('Reset password', [
            'change_password_form[plainPassword][first]' => 'newStrongPassword',
            'change_password_form[plainPassword][second]' => 'newStrongPassword',
        ]);

        self::assertResponseRedirects('/login');

        $user = $this->userRepository->findOneBy(['mail_user' => 'me@example.com']); // ← Utilise mail_user

        self::assertInstanceOf(User::class, $user);

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        self::assertTrue($passwordHasher->isPasswordValid($user, 'newStrongPassword'));
    }

    protected function tearDown(): void
    {
        // ✅ Nettoyer après le test
        $this->cleanDatabase();
        
        parent::tearDown();
        $this->em->close();
    }
}