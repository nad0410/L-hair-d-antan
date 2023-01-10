<?php

namespace App\Controller;

use DateTime;
use App\Form\RDVType;
use App\Entity\Prestations;
use App\Entity\RDV;
use App\Form\PrestationsType;
use App\Repository\RDVRepository;
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
    public function calendar(RDVRepository $rDVRepository): Response
    {
        $events = $rDVRepository->findAll();
        $rdvs = [];
        foreach ($events as $event) {
            $fin_rdv = $event->getDateTime();
            $time = $event->getDateTime()->format('Y-m-d H:i:s');
            $dt = new DateTime($time);
            $dt->modify('+ 120 minutes');
            $rdvs[] = [
                'title' =>  "Nom: " . $event->getNom() . " Prenom: " . $event->getPrenom() . " Tel: " . $event->getTel(),
                'id' => $event->getId(),
                'start' => $event->getDateTime()->format('Y-m-d H:i:s'),
                'end' => $dt->format('Y-m-d H:i:s'),
                'user_id' => $event->getUser()->getName(),
                'nom' => $event->getNom(),
                'prenom' => $event->getPrenom(),
                'email' => $event->getEmail(),
                'tel' => $event->getTel(),
            ];
        }
        $data = json_encode($rdvs);
        return $this->render('admin/calendar.html.twig', compact('data'));
    }

    #[Route('/admin/calendar/{id}/edit', name: 'admin_calendar_edit')]
    public function majEvent($id,PrestationsRepository $prestationsRepository, RDVRepository $rdvRepository, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        $rdv = $rdvRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(RDVType::class, $rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $rdvRepository->removeRdvPrestation(1, "rdv_prestation");
            
            $entityManagerInterface->persist($rdv);
            $entityManagerInterface->flush();
        }
        return $this->render('admin/modifiy_reservation.html.twig', [
            'form' => $form->createView()
        ]);
    }
    // Création de la route Prestations
    #[Route('/admin/prestations', name: 'admin_prestations')]
    public function prestations(PrestationsRepository $prestationsRepository, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $prestation = new Prestations();
        $form = $this->createForm(PrestationsType::class, $prestation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $prestation->setTitle($form->getData()->getTitle() . " ( " . $form->getData()->getTime() . " min )");
            $entityManagerInterface->persist($prestation);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/prestations.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
