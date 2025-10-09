<?php

namespace App\Controller;
use App\Form\ProfileType;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\File\Exception\FileException;



class UserController extends AbstractController
{
    /**  * Page INSCRIPTION */
    #[Route('/registration', name: 'user_registration' , methods: ['GET','POST'])]
    public function registration(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);        

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère le mot de passe saisi
            $plainPassword = $form->get('password_user')->getData();

            // Vérifie le REGEX manuellement pour plus de contrôle
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{14,}$/';
            if (!preg_match($regex, $plainPassword)) {
                $this->addFlash('error', '❌ Le mot de passe doit comporter au moins 14 caractères, avec une majuscule, une minuscule, un chiffre et un caractère spécial.');
                return $this->redirectToRoute('user_registration');
            }

        // Vérifier si l'email existe déjà
        $existingUser = $em->getRepository(User::class)->findOneBy([
            'mail_user' => $user->getMailUser()
        ]);

        if ($existingUser) {
            $this->addFlash('error', 'Cet email est déjà utilisé.');
            return $this->redirectToRoute('user_registration');
        }

        // Hash le mot de passe seulement après que le formulaire soit validé
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $form->get('password_user')->getData()
        );
        $user->setPasswordUser($hashedPassword);
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

     /**  * Page CONNEXION */
    #[Route('/login', name: 'login' , methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        // Récupère l'erreur de connexion si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();

        if ($this->getUser()) {
            // Redirige si l'utilisateur est déjà connecté
            return $this->redirectToRoute('myprofile');
        }

        return $this->render('default/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
        ]);
    }

    /* DECONNEXION */
    #[Route('/logout', name: 'logout')]
    public function logout(): void {
        // Ce code ne sera jamais exécuté car Symfony interceptera la requête avant.
    }





    /**  * Page MON PROFIL */
    #[Route('/myprofile', name: 'myprofile' , methods: ['GET','POST'])]
    public function myprofile(Request $request, UserPasswordHasherInterface $passwordHasher, 
                              EntityManagerInterface $em ): Response  {

        // Vérifie si l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user) {
            // Redirige vers la page de connexion s'il n'est pas connecté
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 🔹 Gestion du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPasswordUser($hashedPassword);
            }

            // 🔹 Gestion de l'avatar
            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                $newFilename = uniqid().'.'.$avatarFile->guessExtension();

                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'), // à définir dans services.yaml
                        $newFilename
                    );
                    $user->setAvatarUser($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
            }

            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('myprofile');
        }

        // Affiche la page du profil avec les données de l'utilisateur
        return $this->render('default/myprofile.html.twig', [
            'user' => $user,
            'form' => $form->createView(), 
        ]);
    }








}



 
   

?>