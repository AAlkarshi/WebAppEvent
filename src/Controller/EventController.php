<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Register;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use App\Form\EventType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class EventController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function list(Request $request, EventRepository $eventRepository , CategoryRepository $categoryRepository): Response 
    {
        
         // 🔹 Récupère toutes les catégories pour la liste déroulante
        $categories = $categoryRepository->findAll();

        // 🔹 Récupère l'ID de catégorie envoyé dans l'URL (GET)
        $categoryId = $request->query->get('id_category');

        $searchCity = $request->query->get('terme');


        // 🔹 Si une catégorie est sélectionnée, on filtre
        if ($categoryId) {
            $events = $eventRepository->findBy(['category' => $categoryId]);
        } else {
            $events = $eventRepository->findAll();
        }

          // 🏙️ Filtrage par ville
        if ($searchCity) {
            $events = array_filter($events, function ($event) use ($searchCity) {
                $address = $event->getAddress();
                return $address && stripos($address->getCity(), $searchCity) !== false;
            });
        }
        

        return $this->render('event/list.html.twig', [
            'events' => $events,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'searchCity' => $searchCity,
        ]);
    
    }


    //CREER MON EVENT
    #[Route('/events/createmyevent', name: 'createmyevent')]
    public function createMyEvent(Request $request, EventRepository $eventRepository, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response {
    $event = new Event();
    $form = $this->createForm(EventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($event);
        $em->flush();

        $this->addFlash('success', 'Événement créé avec succès !');

        // ✅ Ici on retourne bien une Response
        return $this->redirectToRoute('createmyevent');
    }

    return $this->render('event/createmyevent.html.twig', [
        'form' => $form->createView(),
    ]);
}



    // VOIR EVENT OU JE SUIS INSCRIS
   #[Route('/events/registeredevents', name: 'registeredevents')]
    public function registeredEvents(EntityManagerInterface $em): Response {
    $user = $this->getUser();

    $registrations = $em->getRepository(Register::class)->findBy([
        'user' => $user,
        'active' => true
    ]);

    return $this->render('event/registeredevents.html.twig', [
        'registrations' => $registrations,
    ]);
}

    
    // S'INSCRIRE UN EVENT
    #[Route('/events/{id}/register', name: 'event_register')]
    public function registerToEvent(Event $event, EntityManagerInterface $em): Response {
    $user = $this->getUser();

    // Vérifie si l’utilisateur est déjà inscrit
    $existing = $em->getRepository(Register::class)->findOneBy([
        'user' => $user,
        'Event' => $event,
    ]);

    if ($existing && $existing->isActive()) {
        $this->addFlash('info', '⚠️ Vous êtes déjà inscrit à cet événement.');
    } else {
        if (!$existing) {
            $register = new Register();
            $register->setUser($user);
            $register->setEvent($event);
        } else {
            $register = $existing;
        }

        $register->setActive(true);

        $em->persist($register);
        $em->flush();

        $this->addFlash('success', '🎉 Inscription réussie à l’événement !');
    }

    return $this->redirectToRoute('registeredevents');
}






    // SE DESINSCRIRE A UN EVENT
    #[Route('/events/{id}/unregister', name: 'event_unregister')]
    public function unregisterFromEvent(Event $event, EntityManagerInterface $em): Response {
    $user = $this->getUser();

    $register = $em->getRepository(Register::class)->findOneBy([
        'user' => $user,
        'Event' => $event,
    ]);

    if ($register) {
        $register->setActive(false);
        $em->flush();
        $this->addFlash('success', '❌ Vous vous êtes désinscrit de l’événement.');
    } else {
        $this->addFlash('error', 'Aucune inscription trouvée pour cet événement.');
    }

    return $this->redirectToRoute('registeredevents');
}






}
