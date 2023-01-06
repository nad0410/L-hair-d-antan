<?php

namespace App\Controller;

use App\Form\RDVType;
use App\Entity\Prestations;
use App\Form\PrestationsType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/calendar', name: 'admin_calendar')]
    public function calendar(): Response
    {
        return $this->render('admin/calendar.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/prestations', name: 'admin_prestations')]
    public function prestations(PrestationsRepository $prestationsRepository, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $rdv = new Prestations();
        $form = $this->createForm(PrestationsType::class, $rdv);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($rdv);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/prestations.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
