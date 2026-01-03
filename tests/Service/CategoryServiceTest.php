<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Category;
use App\Entity\User;
use App\Service\CategoryService;
use App\Enum\UserRole;
use App\Enum\GenderUser;

class CategoryServiceTest extends KernelTestCase
{
    private $em;
    private CategoryService $categoryService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get('doctrine')->getManager();

        // Instancie le service
        $this->categoryService = new CategoryService($this->em);
    }

    public function testSaveCategory(): void
    {
        // 1️⃣ Crée un utilisateur admin temporaire si nécessaire
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['mail_user' => 'admin_integration@test.fr']);

        if (!$user) {
            $user = new User();
            $user->setMailUser('admin_integration@test.fr');
            $user->setRole(UserRole::Admin);
            $user->setPasswordUser(password_hash('password', PASSWORD_BCRYPT));
            $user->setGenderUser(GenderUser::Homme);
            $user->setFirstnameUser('Admin');
            $user->setLastnameUser('Test');
            $user->setDatebirthUser(new \DateTimeImmutable('1990-01-01'));
            $user->setCityUser('Strasbourg');

            $this->em->persist($user);
            $this->em->flush(); // ⚡ flush pour générer l'ID
        }

        $this->assertNotNull($user->getId(), 'L’utilisateur doit avoir un ID');

        $category = new Category();
        $category->setNameCategory('Catégorie Test Intégration');
        $category->setCreated($user);           // relation ManyToOne
        $category->setCreatedBy($user->getId()); // ⚡ nécessaire pour MySQL


        // 3️⃣ Sauvegarde via le service
        $this->categoryService->saveCategory($category);

        // 4️⃣ Vérifie qu'elle est en base
        $savedCategory = $this->em->getRepository(Category::class)
            ->findOneBy(['name_category' => 'Catégorie Test Intégration']);

        $this->assertNotNull($savedCategory);
        $this->assertSame($user->getId(), $savedCategory->getCreated()->getId());

        // 5️⃣ Nettoyage
        $this->em->remove($savedCategory);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }
}
