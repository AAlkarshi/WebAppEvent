<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/address')]
class AddressController extends AbstractController
{
    #[Route('/', name: 'address_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $addresses = $em->getRepository(Address::class)->findAll();

        return $this->render('address/listAddress.html.twig', [
            'addresses' => $addresses,
        ]);
    }

    #[Route('/new', name: 'address_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($address);
            $em->flush();

            $this->addFlash('success', 'Adresse créée avec succès !');
            return $this->redirectToRoute('address_index');
        }

        return $this->render('address/newAddress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'address_edit', methods: ['GET', 'POST'])]
    public function edit(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Adresse mise à jour !');
            return $this->redirectToRoute('address_index');
        }

        return $this->render('address/edit.html.twig', [
            'form' => $form->createView(),
            'address' => $address,
        ]);
    }

    #[Route('/{id}/delete', name: 'address_delete', methods: ['POST'])]
    public function delete(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $em->remove($address);
            $em->flush();
            $this->addFlash('success', 'Adresse supprimée !');
        }

        return $this->redirectToRoute('address_index');
    }
}
