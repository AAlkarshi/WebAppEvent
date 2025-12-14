<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;


/* TEST généré automatiquement pour vérifier que le processus fonctionne */
class ResetPasswordControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    protected function setUp(): void {
        $this->client = static::createClient();

        // Ensure we have a clean database
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $this->em = $em;

        $this->userRepository = $container->get(UserRepository::class);

        // Nettoyer les catégories avant les utilisateurs
        $this->em->createQuery('DELETE FROM App\Entity\Category')->execute();

        foreach ($this->userRepository->findAll() as $user) {
            $this->em->remove($user);
        }

        $this->em->flush();
    }

      public function testResetPasswordController(): void {
        // Création utilisateur de test
        $user = (new User())
            ->setMailUser('me@example.com')
            ->setPasswordUser('old-password')
            ->setLastnameUser('Doe')
            ->setFirstnameUser('John')
            ->setGenderUser(\App\Enum\GenderUser::Homme)
            ->setDatebirthUser(new \DateTimeImmutable('1990-01-01'))
            ->setCityUser('Paris');

        $this->em->persist($user);
        $this->em->flush();

        // Génération du token via ResetPasswordHelperInterface
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

        // Vérification que le mot de passe a bien été modifié
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
