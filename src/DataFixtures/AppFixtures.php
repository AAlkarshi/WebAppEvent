<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Category;
use App\Entity\Address;
use App\Entity\Register;
use App\Enum\GenderUser;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Récupérer l'admin créé par AdminUserFixture
        $admin = $manager->getRepository(User::class)->findOneBy(['mail_user' => 'admin@test.local']);

        // ========== CRÉATION DES UTILISATEURS ==========
        $users = [];

        // Utilisateur 1
        $user1 = new User();
        $user1->setFirstnameUser('Marie');
        $user1->setLastnameUser('Dupont');
        $user1->setCityUser('Paris');
        $user1->setMailUser('marie.dupont@test.local');
        $user1->setRole(UserRole::User);
        $user1->setGenderUser(GenderUser::Femme);
        $user1->setDatebirthUser(new \DateTimeImmutable('1995-03-15'));
        $user1->setDateCreation(new \DateTimeImmutable());
        $user1->setPasswordUser($this->passwordHasher->hashPassword($user1, 'user123'));
        $manager->persist($user1);
        $users[] = $user1;

        // Utilisateur 2
        $user2 = new User();
        $user2->setFirstnameUser('Pierre');
        $user2->setLastnameUser('Martin');
        $user2->setCityUser('Lyon');
        $user2->setMailUser('pierre.martin@test.local');
        $user2->setRole(UserRole::User);
        $user2->setGenderUser(GenderUser::Homme);
        $user2->setDatebirthUser(new \DateTimeImmutable('1988-07-22'));
        $user2->setDateCreation(new \DateTimeImmutable());
        $user2->setPasswordUser($this->passwordHasher->hashPassword($user2, 'user123'));
        $manager->persist($user2);
        $users[] = $user2;

        // Utilisateur 3
        $user3 = new User();
        $user3->setFirstnameUser('Sophie');
        $user3->setLastnameUser('Bernard');
        $user3->setCityUser('Marseille');
        $user3->setMailUser('sophie.bernard@test.local');
        $user3->setRole(UserRole::User);
        $user3->setGenderUser(GenderUser::Femme);
        $user3->setDatebirthUser(new \DateTimeImmutable('1992-11-08'));
        $user3->setDateCreation(new \DateTimeImmutable());
        $user3->setPasswordUser($this->passwordHasher->hashPassword($user3, 'user123'));
        $manager->persist($user3);
        $users[] = $user3;

        // Utilisateur 4
        $user4 = new User();
        $user4->setFirstnameUser('Thomas');
        $user4->setLastnameUser('Petit');
        $user4->setCityUser('Toulouse');
        $user4->setMailUser('thomas.petit@test.local');
        $user4->setRole(UserRole::User);
        $user4->setGenderUser(GenderUser::Homme);
        $user4->setDatebirthUser(new \DateTimeImmutable('1990-05-30'));
        $user4->setDateCreation(new \DateTimeImmutable());
        $user4->setPasswordUser($this->passwordHasher->hashPassword($user4, 'user123'));
        $manager->persist($user4);
        $users[] = $user4;

        // Utilisateur 5
        $user5 = new User();
        $user5->setFirstnameUser('Julie');
        $user5->setLastnameUser('Moreau');
        $user5->setCityUser('Strasbourg');
        $user5->setMailUser('julie.moreau@test.local');
        $user5->setRole(UserRole::User);
        $user5->setGenderUser(GenderUser::Femme);
        $user5->setDatebirthUser(new \DateTimeImmutable('1994-09-12'));
        $user5->setDateCreation(new \DateTimeImmutable());
        $user5->setPasswordUser($this->passwordHasher->hashPassword($user5, 'user123'));
        $manager->persist($user5);
        $users[] = $user5;

        $manager->flush();

        // ========== CRÉATION DES CATÉGORIES ==========
        $categories = [];
        
        $cat1 = new Category();
        $cat1->setNameCategory('Cinema');
        $cat1->setCreated($admin);
        $manager->persist($cat1);
        $categories[] = $cat1;

        $cat2 = new Category();
        $cat2->setNameCategory('Concert');
        $cat2->setCreated($admin);
        $manager->persist($cat2);
        $categories[] = $cat2;

        $cat3 = new Category();
        $cat3->setNameCategory('Sport');
        $cat3->setCreated($user1);
        $manager->persist($cat3);
        $categories[] = $cat3;

        $cat4 = new Category();
        $cat4->setNameCategory('JeuxVideo');
        $cat4->setCreated($admin);
        $manager->persist($cat4);
        $categories[] = $cat4;

        $cat5 = new Category();
        $cat5->setNameCategory('Poker');
        $cat5->setCreated($user2);
        $manager->persist($cat5);
        $categories[] = $cat5;

        $cat6 = new Category();
        $cat6->setNameCategory('Course');
        $cat6->setCreated($user3);
        $manager->persist($cat6);
        $categories[] = $cat6;

        $cat7 = new Category();
        $cat7->setNameCategory('Restauration');
        $cat7->setCreated($admin);
        $manager->persist($cat7);
        $categories[] = $cat7;

        $cat8 = new Category();
        $cat8->setNameCategory('Sortie');
        $cat8->setCreated($admin);
        $manager->persist($cat8);
        $categories[] = $cat8;


        $cat9 = new Category();
        $cat9->setNameCategory('Promenade');
        $cat9->setCreated($admin);
        $manager->persist($cat9);
        $categories[] = $cat9;

        $cat10 = new Category();
        $cat10->setNameCategory('Jeux de société');
        $cat10->setCreated($admin);
        $manager->persist($cat10);
        $categories[] = $cat10;

        $cat11 = new Category();
        $cat11->setNameCategory('Autre');
        $cat11->setCreated($admin);
        $manager->persist($cat11);
        $categories[] = $cat11;

        // ========== CRÉATION DES ADRESSES ==========
        $addresses = [];

        $addr1 = new Address();
        $addr1->setAddress('15 Rue de la République');
        $addr1->setCity('Paris');
        $addr1->setCp(75001);
        $manager->persist($addr1);
        $addresses[] = $addr1;

        $addr2 = new Address();
        $addr2->setAddress('42 Avenue des Champs-Élysées');
        $addr2->setCity('Paris');
        $addr2->setCp(75008);
        $manager->persist($addr2);
        $addresses[] = $addr2;

        $addr3 = new Address();
        $addr3->setAddress('8 Place Bellecour');
        $addr3->setCity('Lyon');
        $addr3->setCp(69002);
        $manager->persist($addr3);
        $addresses[] = $addr3;

        $addr4 = new Address();
        $addr4->setAddress('23 Vieux Port');
        $addr4->setCity('Marseille');
        $addr4->setCp(13001);
        $manager->persist($addr4);
        $addresses[] = $addr4;

        $addr5 = new Address();
        $addr5->setAddress('10 Place du Capitole');
        $addr5->setCity('Toulouse');
        $addr5->setCp(31000);
        $manager->persist($addr5);
        $addresses[] = $addr5;

        $addr6 = new Address();
        $addr6->setAddress('5 Place Kléber');
        $addr6->setCity('Strasbourg');
        $addr6->setCp(67000);
        $manager->persist($addr6);
        $addresses[] = $addr6;

        // ========== CRÉATION DES ÉVÉNEMENTS ==========
        $events = [];

        // Événement 1 - Cinema
        $event1 = new Event();
        $event1->setTitleEvent('Soirée Cinéma en plein air');
        $event1->setDescriptionEvent('Projection de films classiques sous les étoiles');
        $event1->setDateTimeEvent(new \DateTime('+10 days'));
        $event1->setDurationEvent(180);
        $event1->setNbxParticipantMax(100);
        $event1->setNbxParticipant(1);
        $event1->setAddress($addresses[0]);
        $event1->setCategory($categories[0]); // Cinema
        $event1->setImageEvent('cinema.jpg');
        $event1->setCreatedBy($user1);
        $manager->persist($event1);
        $events[] = $event1;

        // Événement 2 - Concert
        $event2 = new Event();
        $event2->setTitleEvent('Concert Rock au Zénith');
        $event2->setDescriptionEvent('Soirée rock avec les meilleurs groupes français');
        $event2->setDateTimeEvent(new \DateTime('+5 days'));
        $event2->setDurationEvent(240);
        $event2->setNbxParticipantMax(500);
        $event2->setNbxParticipant(1);
        $event2->setAddress($addresses[1]);
        $event2->setCategory($categories[1]); // Concert
        $event2->setImageEvent('concert.jpg');
        $event2->setCreatedBy($user2);
        $manager->persist($event2);
        $events[] = $event2;

        // Événement 3 - Sport
        $event3 = new Event();
        $event3->setTitleEvent('Tournoi de Football Amateur');
        $event3->setDescriptionEvent('Compétition amicale de football à 5');
        $event3->setDateTimeEvent(new \DateTime('+15 days'));
        $event3->setDurationEvent(300);
        $event3->setNbxParticipantMax(30);
        $event3->setNbxParticipant(1);
        $event3->setAddress($addresses[2]);
        $event3->setCategory($categories[2]); // Sport
        $event3->setImageEvent('sport.jpg');
        $event3->setCreatedBy($user3);
        $manager->persist($event3);
        $events[] = $event3;

        // Événement 4 - JeuxVideo
        $event4 = new Event();
        $event4->setTitleEvent('Tournoi E-Sport FIFA 2026');
        $event4->setDescriptionEvent('Compétition de FIFA avec prix à gagner');
        $event4->setDateTimeEvent(new \DateTime('+20 days'));
        $event4->setDurationEvent(360);
        $event4->setNbxParticipantMax(64);
        $event4->setNbxParticipant(1);
        $event4->setAddress($addresses[3]);
        $event4->setCategory($categories[3]); // JeuxVideo
        $event4->setImageEvent('jeuxvideo.jpg');
        $event4->setCreatedBy($admin);
        $manager->persist($event4);
        $events[] = $event4;

        // Événement 5 - Poker
        $event5 = new Event();
        $event5->setTitleEvent('Soirée Poker Texas Hold\'em');
        $event5->setDescriptionEvent('Tournoi de poker amical entre passionnés');
        $event5->setDateTimeEvent(new \DateTime('+7 days'));
        $event5->setDurationEvent(300);
        $event5->setNbxParticipantMax(20);
        $event5->setNbxParticipant(1);
        $event5->setAddress($addresses[4]);
        $event5->setCategory($categories[4]); // Poker
        $event5->setImageEvent('poker.jpg');
        $event5->setCreatedBy($user4);
        $manager->persist($event5);
        $events[] = $event5;

        // Événement 6 - Course
        $event6 = new Event();
        $event6->setTitleEvent('Marathon de Strasbourg');
        $event6->setDescriptionEvent('Course à pied de 42km à travers la ville');
        $event6->setDateTimeEvent(new \DateTime('+12 days'));
        $event6->setDurationEvent(300);
        $event6->setNbxParticipantMax(1000);
        $event6->setNbxParticipant(1);
        $event6->setAddress($addresses[5]);
        $event6->setCategory($categories[5]); // Course
        $event6->setImageEvent('course.jpg');
        $event6->setCreatedBy($user5);
        $manager->persist($event6);
        $events[] = $event6;

        // Événement 7 - Restauration
        $event7 = new Event();
        $event7->setTitleEvent('Dîner Gastronomique');
        $event7->setDescriptionEvent('Soirée dégustation avec chef étoilé');
        $event7->setDateTimeEvent(new \DateTime('+3 days'));
        $event7->setDurationEvent(180);
        $event7->setNbxParticipantMax(40);
        $event7->setNbxParticipant(1);
        $event7->setAddress($addresses[0]);
        $event7->setCategory($categories[6]); // Restauration
        $event7->setImageEvent('restauration.jpg');
        $event7->setCreatedBy($user2);
        $manager->persist($event7);
        $events[] = $event7;

        // Événement 8 - Concert
        $event8 = new Event();
        $event8->setTitleEvent('Festival Jazz');
        $event8->setDescriptionEvent('Trois jours de jazz avec artistes internationaux');
        $event8->setDateTimeEvent(new \DateTime('+25 days'));
        $event8->setDurationEvent(480);
        $event8->setNbxParticipantMax(300);
        $event8->setNbxParticipant(1);
        $event8->setAddress($addresses[1]);
        $event8->setCategory($categories[1]); // Concert
        $event8->setImageEvent('concert.jpg');
        $event8->setCreatedBy($user3);
        $manager->persist($event8);
        $events[] = $event8;

        // Événement 9 - Sport
        $event9 = new Event();
        $event9->setTitleEvent('Match de Basketball');
        $event9->setDescriptionEvent('Match amical de basketball 3x3');
        $event9->setDateTimeEvent(new \DateTime('+8 days'));
        $event9->setDurationEvent(120);
        $event9->setNbxParticipantMax(12);
        $event9->setNbxParticipant(1);
        $event9->setAddress($addresses[2]);
        $event9->setCategory($categories[2]); // Sport
        $event9->setImageEvent('sport.jpg');
        $event9->setCreatedBy($user1);
        $manager->persist($event9);
        $events[] = $event9;

        // Événement 10 - Cinema
        $event10 = new Event();
        $event10->setTitleEvent('Festival du Film d\'Animation');
        $event10->setDescriptionEvent('Projection de films d\'animation japonais');
        $event10->setDateTimeEvent(new \DateTime('+18 days'));
        $event10->setDurationEvent(240);
        $event10->setNbxParticipantMax(80);
        $event10->setNbxParticipant(1);
        $event10->setAddress($addresses[3]);
        $event10->setCategory($categories[0]); // Cinema
        $event10->setImageEvent('cinema.jpg');
        $event10->setCreatedBy($admin);
        $manager->persist($event10);
        $events[] = $event10;

        $manager->flush();

        // ========== CRÉATION DES INSCRIPTIONS ==========

        // User1 inscrit à plusieurs événements
        $register1 = new Register();
        $register1->setUser($user1);
        $register1->setEvent($events[1]); // Concert Rock
        $register1->setActive(true);
        $manager->persist($register1);
        $events[1]->setNbxParticipant($events[1]->getNbxParticipant() + 1);

        $register2 = new Register();
        $register2->setUser($user1);
        $register2->setEvent($events[5]); // Marathon
        $register2->setActive(true);
        $manager->persist($register2);
        $events[5]->setNbxParticipant($events[5]->getNbxParticipant() + 1);

        // User2 inscrit à plusieurs événements
        $register3 = new Register();
        $register3->setUser($user2);
        $register3->setEvent($events[2]); // Football
        $register3->setActive(true);
        $manager->persist($register3);
        $events[2]->setNbxParticipant($events[2]->getNbxParticipant() + 1);

        $register4 = new Register();
        $register4->setUser($user2);
        $register4->setEvent($events[3]); // E-Sport
        $register4->setActive(true);
        $manager->persist($register4);
        $events[3]->setNbxParticipant($events[3]->getNbxParticipant() + 1);

        $register5 = new Register();
        $register5->setUser($user2);
        $register5->setEvent($events[4]); // Poker
        $register5->setActive(true);
        $manager->persist($register5);
        $events[4]->setNbxParticipant($events[4]->getNbxParticipant() + 1);

        // User3 inscrit à plusieurs événements
        $register6 = new Register();
        $register6->setUser($user3);
        $register6->setEvent($events[0]); // Cinéma
        $register6->setActive(true);
        $manager->persist($register6);
        $events[0]->setNbxParticipant($events[0]->getNbxParticipant() + 1);

        $register7 = new Register();
        $register7->setUser($user3);
        $register7->setEvent($events[6]); // Dîner Gastronomique
        $register7->setActive(true);
        $manager->persist($register7);
        $events[6]->setNbxParticipant($events[6]->getNbxParticipant() + 1);

        // User4 inscrit à plusieurs événements
        $register8 = new Register();
        $register8->setUser($user4);
        $register8->setEvent($events[7]); // Festival Jazz
        $register8->setActive(true);
        $manager->persist($register8);
        $events[7]->setNbxParticipant($events[7]->getNbxParticipant() + 1);

        $register9 = new Register();
        $register9->setUser($user4);
        $register9->setEvent($events[5]); // Marathon
        $register9->setActive(true);
        $manager->persist($register9);
        $events[5]->setNbxParticipant($events[5]->getNbxParticipant() + 1);

        $register10 = new Register();
        $register10->setUser($user4);
        $register10->setEvent($events[8]); // Basketball
        $register10->setActive(true);
        $manager->persist($register10);
        $events[8]->setNbxParticipant($events[8]->getNbxParticipant() + 1);

        // User5 inscrit à plusieurs événements
        $register11 = new Register();
        $register11->setUser($user5);
        $register11->setEvent($events[9]); // Film d'animation
        $register11->setActive(true);
        $manager->persist($register11);
        $events[9]->setNbxParticipant($events[9]->getNbxParticipant() + 1);

        $register12 = new Register();
        $register12->setUser($user5);
        $register12->setEvent($events[3]); // E-Sport
        $register12->setActive(true);
        $manager->persist($register12);
        $events[3]->setNbxParticipant($events[3]->getNbxParticipant() + 1);

        $register13 = new Register();
        $register13->setUser($user5);
        $register13->setEvent($events[1]); // Concert Rock
        $register13->setActive(true);
        $manager->persist($register13);
        $events[1]->setNbxParticipant($events[1]->getNbxParticipant() + 1);

        // Admin inscrit à quelques événements
        $register14 = new Register();
        $register14->setUser($admin);
        $register14->setEvent($events[2]); // Football
        $register14->setActive(true);
        $manager->persist($register14);
        $events[2]->setNbxParticipant($events[2]->getNbxParticipant() + 1);

        $register15 = new Register();
        $register15->setUser($admin);
        $register15->setEvent($events[4]); // Poker
        $register15->setActive(true);
        $manager->persist($register15);
        $events[4]->setNbxParticipant($events[4]->getNbxParticipant() + 1);

        $register16 = new Register();
        $register16->setUser($admin);
        $register16->setEvent($events[6]); // Dîner Gastronomique
        $register16->setActive(true);
        $manager->persist($register16);
        $events[6]->setNbxParticipant($events[6]->getNbxParticipant() + 1);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AdminUserFixture::class,
        ];
    }
}