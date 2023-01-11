<?php

namespace App\Controller;

use App\Entity\RDV;
use App\Form\RDVType;
use App\Repository\RDVRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    
    #[Route('/reservation', name: 'reservation')]
    public function reservation(RDVRepository $rDVRepository, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $rdv = new RDV();
        
        $form = $this->createForm(RDVType::class, $rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $rdv->addRdvPrestation($form->get('prestation')->getData());
            $entityManagerInterface->persist($rdv);
            $entityManagerInterface->flush();
        }

        return $this->render('default/reservation.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
