<?php

// TEST UNITAIRE 
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Enum\UserRole;
use App\Enum\GenderUser;
use App\Entity\Category;

    class CategoryControllerTest extends WebTestCase {

    //Ce test vérifie qu’un USER avec le rôle ADMIN peut accéder à la page.
    public function testCreateCategoryPageRequiresLogin(): void{
        $client = static::createClient();
        $client->request('GET', '/category/create');

        // Vérifie que les utilisateurs non connectés sont redirigés vers le login
        $this->assertResponseRedirects('/login');
    }

    public function testCreateCategoryAsAdmin(): void {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();

        // 🔹 Nettoyage préalable pour éviter les doublons
        $existingCategory = $em->getRepository(Category::class)->findOneBy(['name_category' => 'Catégorie test']);
        if ($existingCategory) {
            $em->remove($existingCategory);
            $em->flush();
        }

        // 🔹 Récupère ou crée un utilisateur admin
        $user = $em->getRepository(User::class)->findOneBy(['mail_user' => 'admin@test.fr']);
        if (!$user) {
            $user = new User();
            $user->setMailUser('admin@test.fr');
            $user->setRole(UserRole::Admin);
            $user->setPasswordUser(password_hash('password', PASSWORD_BCRYPT));
            $user->setGenderUser(GenderUser::Homme);
            $user->setFirstnameUser('Admin');
            $user->setLastnameUser('Test');
            $user->setDatebirthUser(new \DateTimeImmutable('1990-01-01'));
            $user->setCityUser('Strasbourg');
            $em->persist($user);
            $em->flush();
        }

        // 🔹 Connecte l'utilisateur admin
        $client->loginUser($user);

        // 🔹 Accède à la page de création
        $crawler = $client->request('GET', '/category/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Créer une catégorie');

        // 🔹 Soumet le formulaire
        $form = $crawler->selectButton('Créer')->form([
            'category[name_category]' => 'Catégorie test',
        ]);
        $client->submit($form);

        // 🔹 Vérifie la redirection et le message flash
        $this->assertResponseRedirects('/category/create');
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'La catégorie a été créée avec succès');

        // ✅ Vérifie que la catégorie est bien enregistrée en base
        $createdCategory = $em->getRepository(Category::class)->findOneBy(['name_category' => 'Catégorie test']);
        $this->assertNotNull($createdCategory, 'La catégorie devrait être enregistrée dans la base de données.');

        // ✅ Vérifie que la catégorie est bien liée à l'admin connecté
        $this->assertSame($user->getId(), $createdCategory->getCreatedBy());
    }
}


