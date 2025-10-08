<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function list(): Response
    {
        // Ici tu récupères les événements depuis la base, par exemple avec EventRepository
        // $events = $eventRepository->findAll();

        return $this->render('event/list.html.twig', [
            // 'events' => $events
        ]);
    }
}
