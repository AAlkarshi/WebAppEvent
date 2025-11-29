<?php

namespace App\Tests\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Enum\GenderUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class EventControllerTest extends WebTestCase {
    private $client;
    private $entityManager;


    // Méthode qui exécute avant chaque test
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }


/*
    // Vérifier la page /events s’affiche correctement & Vérifier qu’un contenu HTML est rendu
    public function testEventListPageLoadsSuccessfully(): void {
        $crawler = $this->client->request('GET', '/events');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body'); // Page rendue
    }
*/

    // Créer un EVENT dans BDD test & Vérifier qu’un USER non connecté qui tente de s’inscrire à un EVENT est redirigé vers la page de connexion /login
    public function testRedirectToLoginWhenNotAuthenticated(): void {
        // Création d'un EVENT
        $event = new Event();
        $event->setTitleEvent('Test Event');
        $event->setDateTimeEvent(new \DateTime('+1 day'));
        $event->setNbxParticipant(1);
        $event->setNbxParticipantMax(5);

        // Ajout d'une catégorie
        $category = $this->entityManager->getRepository(\App\Entity\Category::class)->findOneBy([]);
        if (!$category) {
            $category = new \App\Entity\Category();
            $category->setNameCategory('Test Catégorie');
            $this->entityManager->persist($category);
        }
        $event->setCategory($category);

        // Ajout d’une adresse obligatoire
        $address = new \App\Entity\Address();
        $address->setAddress('10 rue de Test');
        $address->setCity('Strasbourg');
        $address->setCp('67000');
        $this->entityManager->persist($address);
        $event->setAddress($address);

        // Récupération du service de hashage
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        // Création d’un utilisateur factice avec un email unique
        $user = new \App\Entity\User();
        $user->setGenderUser(GenderUser::Homme);
        $user->setFirstnameUser('Test');
        $user->setLastnameUser('User');
        $user->setCityUser('New York');

        // 🔹 Email unique pour éviter les doublons
        $user->setMailUser('fakeuser_' . uniqid() . '@test.fr');

        // 🔹 Hash du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, 'Password_123456');
        $user->setPasswordUser($hashedPassword);

        $user->setDatebirthUser(new \DateTimeImmutable('1999-12-12'));

        // Persistance des entités
        $this->entityManager->persist($user);
        $event->setCreatedBy($user);
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        // Test : un utilisateur non connecté tente de s’inscrire à l’event
        $this->client->request('GET', '/events/' . $event->getId() . '/register');

        // Vérifie qu’il est redirigé vers la page de login
       $this->assertResponseRedirects($this->client->getContainer()->get('router')->generate('login'));

}



/*

    public function testAdminCanDeleteEvent(): void {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $adminUser = $userRepository->findOneByRole('ROLE_ADMIN');

        if (!$adminUser) {
            $this->markTestSkipped('⚠️ Aucun utilisateur admin trouvé dans la base de test.');
        }

        $this->client->loginUser($adminUser);

        $event = new Event();
        $event->setTitleEvent('Test Event to Delete');
        $event->setDateTimeEvent(new \DateTime('+1 day'));
        $event->setNbxParticipant(1);
        $event->setNbxParticipantMax(5);
        $event->setCreatedBy($adminUser);

        $category = $this->entityManager->getRepository(\App\Entity\Category::class)->findOneBy([]);
        if (!$category) {
            $category = new \App\Entity\Category();
            $category->setNameCategory('Test Category');
            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }
        $event->setCategory($category);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        // Simulation de la requête POST de suppression
        $this->client->request('POST', '/event/delete/' . $event->getId(), [
            '_token' => $this->client->getContainer()->get('security.csrf.token_manager')->getToken('delete_event_' . $event->getId()),
        ]);

        $this->assertResponseRedirects('/events');
    }


    public function testUserCanAccessCreateEventPage(): void {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@test.fr');

        if (!$testUser) {
            $this->markTestSkipped('⚠️ Aucun utilisateur de test trouvé.');
        }

        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/events/createmyevent');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }


    public function testCannotCreateEventWhenParticipantsExceedLimit(): void {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('admin@test.fr');

        if (!$user) {
            $this->markTestSkipped('⚠️ Aucun utilisateur de test trouvé.');
        }

        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/events/createmyevent');

        $form = $crawler->selectButton('Créer')->form([
            'event[title_event]' => 'Événement test',
            'event[nbx_participant]' => 10,
            'event[nbx_participant_max]' => 5,
            'event[category_id]' => 2,    
            'event[created_by_id]' => 7,  
            'event[date_time_event]' => 90,
            'event[address_id]' => 4,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/events/createmyevent');
    }






    public function testAnyLoggedInUserCanAccessCreateEventPage(): void {
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Chercher un utilisateur existant
        $testUser = $userRepository->findOneBy([]);
        
        // Si aucun utilisateur, créer un utilisateur "factice"
        if (!$testUser) {
            $testUser = new User();
            $testUser->setFirstnameUser('Test');
            $testUser->setLastnameUser('User');
            $testUser->setMailUser('testuser@example.com');
            $testUser->setPasswordUser('Password123!');
            $testUser->setGenderUser(GenderUser::Homme);
            $testUser->setDatebirthUser(new \DateTimeImmutable('1999-01-01'));
            
            $this->entityManager->persist($testUser);
            $this->entityManager->flush();
        }

        // Login de l'utilisateur
        $this->client->loginUser($testUser);

        // Accès à la page de création d'event
        $crawler = $this->client->request('GET', '/events/createmyevent');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
}
*/
}
