<?php

namespace App\Controller\Admin;

use App\Entity\Prestations;
use App\Form\PrestationsType;
use Symfony\Component\Routing\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    
    // Création de la route "Prestations"
    #[Route('/admin/prestations', name: 'admin_prestations')]
    public function prestations(PrestationsRepository $prestationsRepository): Response
    {
        $prestation = $prestationsRepository->findAll();

        return $this->render('admin/prestations/read.html.twig', [
            'prestations' => $prestation
        ]);
    }

    // Création de la route "Création de Prestations"
    #[Route('/admin/prestations/new', name: 'admin_create_prestations')]
    public function new_prestations(PrestationsRepository $prestationsRepository, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {

        $prestation = new Prestations();
        $form = $this->createForm(PrestationsType::class, $prestation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManagerInterface->persist($prestation);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/prestations/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
