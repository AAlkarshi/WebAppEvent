<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Entity\Category;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;

class CategoryServiceUnitTest extends TestCase
{
    public function testSaveCategory(): void
    {
        // Mock de l'EntityManager pour ne pas toucher à la vraie bdd
        $emMock = $this->createMock(EntityManagerInterface::class);

        // On s'assure que persist() et flush() seront appelés une fois
        $emMock->expects($this->once())
               ->method('persist')
               ->with($this->isInstanceOf(Category::class));

        $emMock->expects($this->once())
               ->method('flush');

        // Instancie le service avec le mock
        $categoryService = new CategoryService($emMock);

        // Crée une catégorie factice
        $category = new Category();
        $category->setNameCategory('Catégorie Unitaire');

        // Appelle la méthode à tester
        $categoryService->saveCategory($category);

        // Ici, on pourrait vérifier que le nom est correct
        $this->assertSame('Catégorie Unitaire', $category->getNameCategory());
    }
}
