<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController {
    //Page ACCUEIL
    #[Route('/' , name: 'default_home', methods: ['GET'])]
    public function home(EventRepository $event_repository) {

        #Recupérer les 3 derniers événements
        $events = $event_repository->findBy([], [], 3);

        return $this->render('default/home.html.twig', [
            'events' => $events, #Je passe la variable events à Twig
        ]);
    }


   


    /**
     * page CATEGORIES DES EVENTS
     * ex : https://localhost:8000/categorie/1
     */
    #[Route('/categorie/{id}' , name: 'default_category', methods: ['GET'])]
    public function category($id , CategoryRepository $categoryRepository) {
        #return new Response("<h1>Catégorie : $type </h1>");
        return $this->render('default/category.html.twig' ,
            [
                'category' => $category
            ]);
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
