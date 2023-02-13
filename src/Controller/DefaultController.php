<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\RDV;
use App\Form\RDVType;
use App\Repository\BijouxRepository;
use App\Repository\CategoryPrestationRepository;
use App\Repository\CategoryProduitsRepository;
use App\Repository\PrestationsRepository;
use App\Repository\ProduitsRepository;
use App\Repository\RDVRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    // ===========================PAGE ACCUEIL===========================
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
    // ===========================PAGE PRESTATIONS===========================
    #[Route('/prestations/{id}', name: 'prestations')]
    public function prestation_category($id, PrestationsRepository $prestationsRepository, CategoryPrestationRepository $CTGPrestationsRepo): Response
    {
        $prestations = $prestationsRepository->findBy(['category' => $id]); // On recupère tous les produits ayant comme category l'id {id} 
        $categories = $CTGPrestationsRepo->findAll(); // On recupère tous les categories de prestation qui servira à créer un selecteur pour pouvoir afficher que certaine prestation

        return $this->render('default/prestations/index.html.twig', [ // On fais un rendu vers la vue spécifier
            'prestations' => $prestations,  // On envoie les informations récupérer avec $prestationsRepository->findAll() vers la vue twig avec comme variable "produits"
            'categories' => $categories, // On envoie les informations récupérer avec $CTGPrestationsRepo->findAll() vers la vue twig avec comme variable "catégories"
        ]);
    }
    // ===========================PAGE PRODUITS===========================

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
        $categories = $CTGProduitsRepository->findAll(); // On recupère tous les categories_Produits qui servira à créer un selecteur pour pouvoir afficher que certain produits

        return $this->render('default/produits/index.html.twig', [ // On fais un rendu vers la vue spécifier
            'produits' => $produits,  // On envoie les informations récupérer avec $produitsRepository->findAll() vers la vue twig avec comme variable "produits"
            'categories' => $categories, // On envoie les informations récupérer avec $CTGProduitsRepository->findAll() vers la vue twig avec comme variable "catégories"
        ]);
    }

    // ===========================PAGE BIJOUX===========================


    #[Route('/bijoux', name: 'bijoux')]
    public function bijoux(BijouxRepository $bijouxRepository): Response
    {
        $bijoux = $bijouxRepository->findAll(); // On recupère tous les bijoux depuis la base de données

        return $this->render('default/bijoux/index.html.twig', [ // On fais un rendu vers la vue spécifier
            'bijoux' => $bijoux,  // On envoie les informations récupérer avec $bijouxRepository->findAll() vers la vue twig avec comme variable "bijoux"
        ]);
    }

    // ===========================PAGE RESERVATION===========================

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

    // ===========================PAGE CONTACT===========================

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $contact = new Contact();
        $form = $this->createFormBuilder($contact)
            ->add("email", EmailType::class, [
                'attr' => ['class' => "contact-form-email"],
            ])
            ->add("message", TextType::class, [
                'attr' => ['class' => "contact-form-text-area"],
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setVue(false);
            $entityManagerInterface->persist($contact);
            $entityManagerInterface->flush();
        }

        return $this->render('default/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('default/cgu.html.twig', []);
    }
}
