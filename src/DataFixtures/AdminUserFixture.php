<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\GenderUser;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixture extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setFirstnameUser('Admin');
        $admin->setLastnameUser('AdminLastName');
        $admin->setCityUser('Paris');
        $admin->setMailUser('admin@test.local');
        $admin->setRole(UserRole::Admin);
        $admin->setGenderUser(GenderUser::Homme);
        $admin->setDatebirthUser(new \DateTimeImmutable('1990-01-01'));
        $admin->setDateCreation(new \DateTimeImmutable());

        $admin->setPasswordUser(
            $this->passwordHasher->hashPassword(
                $admin,
                'admin123'
            )
        );

        $manager->persist($admin);
        $manager->flush();
    }
}
