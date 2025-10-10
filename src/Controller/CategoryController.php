<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{
    #[Route('/category/create', name: 'createCategory')]
    #[IsGranted('ROLE_ADMIN')] // Seul l'admin peut accéder
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();

        // On peut pré-remplir l'utilisateur créateur
        $category->setCreated($this->getUser());
        $category->setCreatedBy($this->getUser()->getId());

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès !');

            return $this->redirectToRoute('createCategory'); // Ou vers la liste des catégories
        }

        return $this->render('category/createCategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/list', name: 'listCategories')]
    public function list(EntityManagerInterface $em): Response {
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }
}
