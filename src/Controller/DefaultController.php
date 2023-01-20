<?php

namespace App\Controller;

use App\Entity\RDV;
use App\Form\RDVType;
use App\Repository\CategoryProduitsRepository;
use App\Repository\ProduitsRepository;
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
            if ($form->get('check_cgu')->getData() == true) {


                $rdv->addRdvPrestation($form->get('prestation')->getData());

                if ($form->get('prestation2')->getData()) {
                    $rdv->addRdvPrestation($form->get('prestation2')->getData());
                };

                if ($form->get('prestation3')->getData() and $form->get('prestation3')->getData() != 0) {
                    $rdv->addRdvPrestation($form->get('prestation3')->getData());
                };

                $entityManagerInterface->persist($rdv);
                $entityManagerInterface->flush();
            } else {
                $this->addFlash("error", "Merci d'accepter les CGU afin de valider votre réservation");
            }
        }

        return $this->render('default/reservation.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/produits', name: 'produits')]
    public function produits(ProduitsRepository $produitsRepository, CategoryProduitsRepository $CTGProduitsRepository): Response
    {
        $produits = $produitsRepository->findAll();
        $categories = $CTGProduitsRepository->findAll();

        return $this->render('produits/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
        ]);
    }

    #[Route('/produits/category/{id}', name: 'produits_category')]
    public function produits_category($id, ProduitsRepository $produitsRepository, CategoryProduitsRepository $CTGProduitsRepository): Response
    {
        $produits = $produitsRepository->findBy(['category' => $id]);
        $categories = $CTGProduitsRepository->findAll();
        return $this->render('produits/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
