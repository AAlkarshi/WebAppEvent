<?php

namespace App\Tests;

use App\Entity\User;
use App\Enum\GenderUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ResetPasswordControllerTest extends WebTestCase
{
    private $client;
    private $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get('doctrine')->getManager();

        // Nettoyage BDD : toujours supprimer les enfants avant les parents
        $tables = [
            'reset_password_request', // enfants de user
            'event',                  // enfants de category
            'register',
            'category',               // parent
            'user'                    // parent
        ];

        $conn = $this->em->getConnection();
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $t) {
            $conn->executeStatement("TRUNCATE TABLE `$t`");
        }
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function testResetPasswordController(): void
    {
        // Création utilisateur
        $user = (new User())
            ->setMailUser('me@example.com')
            ->setPasswordUser('old-password')
            ->setLastnameUser('Doe')
            ->setFirstnameUser('John')
            ->setGenderUser(GenderUser::Homme)
            ->setDatebirthUser(new \DateTimeImmutable('1990-01-01'))
            ->setCityUser('Paris');

        $this->em->persist($user);
        $this->em->flush();

        // Génération token via ResetPasswordHelperInterface (pas d'email)
        $resetHelper = static::getContainer()->get(ResetPasswordHelperInterface::class);
        $resetToken = $resetHelper->generateResetToken($user);

        // Accès au formulaire de reset
        $this->client->request('GET', '/reset-password/reset/' . $resetToken->getToken());
        $this->assertResponseRedirects('/reset-password/reset');

        $crawler = $this->client->followRedirect();

        // Soumission du nouveau mot de passe
        $form = $crawler->filter('form')->form([
            'change_password_form[plainPassword][first]' => 'newStrongPassword123',
            'change_password_form[plainPassword][second]' => 'newStrongPassword123',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/login');

        // Vérification du mot de passe
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['mail_user' => 'me@example.com']);
        $this->assertInstanceOf(User::class, $user);

        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($passwordHasher->isPasswordValid($user, 'newStrongPassword123'));
    }

       protected function tearDown(): void{
        // Nettoyer les données dans le bon ordre
        if ($this->em) {
            $this->em->createQuery('DELETE FROM App\Entity\Category')->execute();
            $this->em->createQuery('DELETE FROM App\Entity\User')->execute();
            
            $this->em->close();
        }
        
        parent::tearDown();
    }

}
