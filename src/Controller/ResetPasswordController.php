<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;


use Symfony\Component\Mailer\Transport;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request', methods: ['GET', 'POST'])]
    public function request(Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('mail_user')->getData();

            return $this->processSendingPasswordResetEmail($email, $translator);
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }


    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, ?string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode(hash) the plain password, and set it.
            $user->setPasswordUser($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }





private function processSendingPasswordResetEmail(string $emailFormData, TranslatorInterface $translator): RedirectResponse {
    $user = $this->entityManager->getRepository(User::class)->findOneBy([
        'mail_user' => $emailFormData,
    ]);

    if (!$user) {
        return $this->redirectToRoute('app_check_email');
    }

    try {
        $this->entityManager
            ->getRepository(\App\Entity\ResetPasswordRequest::class)
            ->removeResetRequest($user);

        // Force le flush pour s'assurer de la suppression
        $this->entityManager->flush();

        $resetToken = $this->resetPasswordHelper->generateResetToken($user);
    } catch (ResetPasswordExceptionInterface $e) {
        // 🔹 DEBUG : Voir quelle exception est levée
        $this->addFlash('error', 'Erreur reset : ' . $e->getMessage());
        
        return $this->redirectToRoute('app_check_email');
    }

    // 🔹 Mailer direct vers Mailtrap
    $dsn = $_ENV['MAILER_DSN'] ?? 'smtp://cb0c7fe78c994c:78ddfe43a5963d@sandbox.smtp.mailtrap.io:587?encryption=tls';
    $transport = Transport::fromDsn($dsn);
    $mailer = new \Symfony\Component\Mailer\Mailer($transport);

    $email = (new TemplatedEmail())
        ->from('alkarshi.abdullrahman@gmail.com')
        ->to($user->getMailUser())
        ->subject('Réinitialisation de votre mot de passe')
        ->text('Pour réinitialiser votre mot de passe, cliquez sur le lien envoyé dans le corps HTML.')
        ->htmlTemplate('reset_password/email.html.twig')
        ->context([
        'resetToken' => $resetToken,
    ]);

    try {
        $mailer->send($email);
    } catch (\Exception $e) {
        $this->addFlash('reset_password_error', $e->getMessage());
    }

    $this->setTokenObjectInSession($resetToken);
    return $this->redirectToRoute('app_check_email');
}









    //TEST DSN  MAILTRAP
    #[Route('/test-mail', name: 'app_test_mail')]
    public function testMail(): Response {
        $dsn = 'smtp://cb0c7fe78c994c:78ddfe43a5963d@sandbox.smtp.mailtrap.io:587?encryption=tls';
        $transport = Transport::fromDsn($dsn);
        $mailer = new \Symfony\Component\Mailer\Mailer($transport);

        $email = (new TemplatedEmail())
            ->from('no-reply@eventify.com')
            ->to('1cc2062086-2a8d2c+user1@inbox.mailtrap.io')
            ->subject('Test Mailtrap depuis ResetPasswordController')
            ->text('Ceci est un test pour vérifier la configuration Mailer.');

        $mailer->send($email);

        return new Response('E-mail envoyé ! Vérifie ton inbox Mailtrap.');
    }







}
