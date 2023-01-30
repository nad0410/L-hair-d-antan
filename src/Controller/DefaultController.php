<?php

namespace App\Controller;

use App\Entity\RDV;
use App\Form\RDVType;
use App\Repository\CategoryProduitsRepository;
use App\Repository\PrestationsRepository;
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
        $produits = $produitsRepository->findAll(); // On recupère tous les produits depuis la base de données
        $categories = $CTGProduitsRepository->findAll(); // On recupère tous les categories_Produits qui servira à créer un selecteur pour pouvoir afficher que les produits 

        return $this->render('default/produits/index.html.twig', [ // On fais un rendu vers la vue spécifier
            'produits' => $produits,  // On envoie les informations récupérer avec $produitsRepository->findAll() vers la vue twig avec comme variable "produits"
            'categories' => $categories, // On envoie les informations récupérer avec $CTGProduitsRepository->findAll() vers la vue twig avec comme variable "catégories"
        ]);
    }

    #[Route('/produits/category/{id}', name: 'produits_category')]
    public function produits_category($id, ProduitsRepository $produitsRepository, CategoryProduitsRepository $CTGProduitsRepository): Response
    {
        $produits = $produitsRepository->findBy(['category' => $id]); // On recupère tous les produits ayant comme category l'id {id} 
        $categories = $CTGProduitsRepository->findAll(); // On recupère tous les categories_Produits qui servira à créer un selecteur pour pouvoir afficher que les produits

        return $this->render('default/produits/index.html.twig', [ // On fais un rendu vers la vue spécifier
            'produits' => $produits,  // On envoie les informations récupérer avec $produitsRepository->findAll() vers la vue twig avec comme variable "produits"
            'categories' => $categories, // On envoie les informations récupérer avec $CTGProduitsRepository->findAll() vers la vue twig avec comme variable "catégories"
        ]);
    }


    #[Route('/prestations', name: 'prestations')]
    public function prestations(PrestationsRepository $prestationsRepository): Response
    {
        $prestations = $prestationsRepository->findAll(); // On recupère tous les produits depuis la base de données

        return $this->render('default/prestations/index.html.twig', [ // On fais un rendu vers la vue spécifier
            'prestations' => $prestations,  // On envoie les informations récupérer avec $prestationsRepository->findAll() vers la vue twig avec comme variable "produits"
        ]);
    }


    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('default/cgu.html.twig', [
        ]);
    }
}
