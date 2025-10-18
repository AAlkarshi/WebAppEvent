<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController {
    
    #[Route('/a-propos', name: 'about')]
    public function about(): Response
    {
        return $this->render('static/about.html.twig');
    }

    #[Route('/mentions-legales', name: 'mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('static/mentions_legales.html.twig');
    }

    #[Route('/confidentialite', name: 'confidentialite')]
    public function confidentialite(): Response
    {
        return $this->render('static/confidentialite.html.twig');
    }

    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('static/cgu.html.twig');
    }







    /**
     * page pour AFFICHER UN EVENT
     * ex : https://localhost:8000/spectacle/raclette_875324
     * ex : https://localhost:8000/{param:category}/{param:title}_{param:id}
     */
    #[Route('/{category}/{title}_{id}' , name: 'default_event', methods: ['GET'])]
    public function event($category, $title, $id) : Response {
        return new Response(
            "<h1>
                    Catégorie : $category
                    <br> Title : $title
                    <br> ID : $id
                    </h1>"
        );
    }
}
