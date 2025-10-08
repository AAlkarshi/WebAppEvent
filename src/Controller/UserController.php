<?php

// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * Page INSCRIPTION
    */

    #[Route('/registration', name: 'user_registration' , methods: ['GET'])]
    public function registration(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
        $avatarFile = $form->get('avatar_user')->getData();

        if ($avatarFile) {
            $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = uniqid().'.'.$avatarFile->guessExtension();

            // Déplace le fichier dans public/uploads/avatars
            $avatarFile->move(
                $this->getParameter('avatars_directory'), // paramètre à définir
                $newFilename
            );

            $user->setAvatarUser($newFilename);
        }

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Inscription réussie !');

        return $this->redirectToRoute('user_registration');
        }


        return $this->render('default/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}



 
   

?>