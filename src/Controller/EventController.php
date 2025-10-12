<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Register;

use App\Entity\Address;


use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use App\Form\EventType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

//PAGINATION
use Knp\Component\Pager\PaginatorInterface;

class EventController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function list(Request $request, PaginatorInterface $paginator, EventRepository $eventRepository , CategoryRepository $categoryRepository): Response {
        // 🔹 Récupère toutes les catégories pour la liste déroulante
        $categories = $categoryRepository->findAll();

        // 🔹 Récupère l'ID de catégorie envoyé dans l'URL (GET)
        $categoryId = $request->query->get('id_category');

        // 🔹 Récupère la ville recherchée
        $searchCity = $request->query->get('terme');

        // 🔹 Récupère les dates de filtre
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        // 🔹 Construction de la requête avec QueryBuilder
        $qb = $eventRepository->createQueryBuilder('e')
            ->leftJoin('e.address', 'a')->addSelect('a')
            ->leftJoin('e.category', 'c')->addSelect('c');

        // 🔹 Si une catégorie est sélectionnée, on filtre
        if ($categoryId) {
            $qb->andWhere('c.id = :catId')
            ->setParameter('catId', $categoryId);
        }

        // 🏙️ Filtrage par ville
        if ($searchCity) {
            $qb->andWhere('LOWER(a.city) LIKE :city')
            ->setParameter('city', '%'.mb_strtolower($searchCity).'%');
        }

        // 🔹 Filtrage par date
        if ($startDate) {
            $qb->andWhere('e.dateTime_event >= :start')
            ->setParameter('start', new \DateTime($startDate));
        }
        if ($endDate) {
            $end = new \DateTimeImmutable($endDate . ' 23:59:59'); // inclure toute la journée
            $qb->andWhere('e.dateTime_event <= :end')
            ->setParameter('end', $end);
        }

        // 🔹 Tri par date croissante
        $qb->orderBy('e.dateTime_event', 'ASC');

        // 🔹 Exécution de la requête
        $events = $qb->getQuery()->getResult();


        // 🔹 Pagination
        $pagination = $paginator->paginate(
            $qb, // Query ou QueryBuilder
            $request->query->getInt('page', 1), // Numéro de page
            8 // Nombre d'événements par page
        );

        // 🔹 Rendu Twig
        return $this->render('event/list.html.twig', [
            'events' => $pagination,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'searchCity' => $searchCity,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

}


    //CREER MON EVENT
    #[Route('/events/createmyevent', name: 'createmyevent')]
public function createMyEvent(Request $request, EventRepository $eventRepository, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response {
    $event = new Event();

    $event->setCreatedBy($this->getUser());


    $form = $this->createForm(EventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        dump($form->get('dateTime_event')->getData());
    }

    if ($form->isSubmitted() && $form->isValid()) {
        // Si des infos pour une nouvelle adresse sont renseignées
        $newAddressData = [
            'address' => $form->get('new_address')->getData(),
            'city' => $form->get('new_city')->getData(),
            'cp' => $form->get('new_cp')->getData(),
        ];

        if (!empty($newAddressData['address']) && !empty($newAddressData['city']) && !empty($newAddressData['cp'])) {
            $address = new Address();
            $address->setAddress($newAddressData['address'])
                    ->setCity($newAddressData['city'])
                    ->setCp($newAddressData['cp']);

            
            $em->persist($address);
            $event->setAddress($address); // lie l'événement à la nouvelle adresse
        } else {
            // Sinon utilise l'adresse sélectionnée
            $event->setAddress($form->get('address')->getData());
        }

        $em->persist($event);
        $em->flush();

        $this->addFlash('success', 'Événement créé avec succès !');
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
public function registerToEvent(Event $event, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    // 🚫 Si l'utilisateur n'est pas connecté → redirection vers la page de connexion
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // 🚫 Empêche le créateur de s’inscrire à son propre événement
    if ($event->getCreatedBy() === $user) {
        $this->addFlash('error', '❌ Vous ne pouvez pas vous inscrire à votre propre événement.');
    } 
    else {
        // 🔍 Vérifie si l’utilisateur est déjà inscrit
        $existing = $em->getRepository(Register::class)->findOneBy([
            'user' => $user,
            'Event' => $event,
        ]);

        if ($existing && $existing->isActive()) {
            // ⚠️ Message affiché directement sur la même page
            $this->addFlash('warning', '⚠️ Vous êtes déjà inscrit à cet événement.');
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
    }

    // 👇 Redirection vers la page actuelle (liste des événements)
    return $this->redirectToRoute('app_events');
}







    // SE DESINSCRIRE A UN EVENT
    #[Route('/events/{id}/unregister', name: 'event_unregister')]
    public function unregisterFromEvent(Event $event, EntityManagerInterface $em): Response {
        $user = $this->getUser();

        // Cherche l'inscription correspondante
        $register = $em->getRepository(Register::class)->findOneBy([
            'user' => $user,
            'Event' => $event,
        ]);

        if ($register) {
            // Supprime l'inscription de la base de données
            $em->remove($register);
            
            $em->flush();
            $this->addFlash('success', '❌ Vous vous êtes désinscrit de l’événement.');
        } else {
            $this->addFlash('error', 'Aucune inscription trouvée pour cet événement.');
        }

        return $this->redirectToRoute('registeredevents');
}





// LISTE DES MES EVENEMNTS QUE J'AI CREER
    #[Route('/myevents', name: 'myevents')]
        public function myEvents(EventRepository $eventRepository): Response {
        $user = $this->getUser();

        // 🔹 Récupère tous les événements créés par l'utilisateur
        $events = $eventRepository->findBy([
            //Celui qui stocke le id de user qui l'a créer en BDD
            'createdBy' => $user, 
        ], [
            'dateTime_event' => 'ASC' 
        ]);

        return $this->render('event/myevents.html.twig', [
            'events' => $events,
        ]);
}






// MODIFIER MON EVENT
#[Route('/event/{id}/edit', name: 'editmyevent')]
public function editmyevent(Event $event, Request $request, EntityManagerInterface $em): Response {
    if ($event->getCreatedBy() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à modifier cet événement.");
    }


    $form = $this->createForm(EventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        $this->addFlash('success', 'Événement modifié avec succès.');
        return $this->redirectToRoute('app_events');
    }

    return $this->render('event/editevent.html.twig', [
        'form' => $form->createView(),
        'event' => $event,
    ]);
}

// SUPP MON EVENT
#[Route('/event/{id}/delete', name: 'deletemyevent')]
public function deletemyevent(Event $event, EntityManagerInterface $em): Response {
    if ($event->getCreatedBy() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à modifier cet événement.");
    }


    $em->remove($event);
    $em->flush();

    $this->addFlash('success', 'Événement supprimé avec succès.');
    return $this->redirectToRoute('app_events');
}




// VOIR UN EVENT EN PARTICULIER DE MA LISTE D'EVENT PAR USER
#[Route('/event/{id}', name: 'view_myevent', methods: ['GET'])]
public function viewMyEvent(Event $event): Response {
    // Vérifie que l'utilisateur connecté est bien le créateur
    if ($event->getCreatedBy() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à voir cet événement.");
    }

    $category = $event->getCategory(); // charge la catégorie
    $address = $event->getAddress();   // charge l'adresse


    // Affiche la page Twig avec les détails de l'événement
    return $this->render('event/viewmyevent.html.twig', [
        'event' => $event,
    ]);
}




//VOIR LES NIFOS D'UN EVENT AFIN DE S'Y INFORMER
#[Route('/events/{id}', name: 'viewthisevent', methods: ['GET'])]
public function viewthisEvent(Event $event): Response {

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    return $this->render('event/viewthisevent.html.twig', [
        'event' => $event,
    ]);
}



}
