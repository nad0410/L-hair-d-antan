<?php

namespace App\Controller;

use DateTime;
use App\Entity\RDV;
use App\Entity\User;
use App\Form\RDVType;
use App\Entity\Bijoux;
use App\Entity\CategoryPrestation;
use App\Entity\Produits;
use App\Form\BijouxType;
use App\Form\ProduitsType;
use App\Entity\Prestations;
use App\Form\CTGProduitsType;
use App\Form\PrestationsType;
use Doctrine\DBAL\Connection;
use App\Entity\CategoryProduits;
use App\Form\CategoryPrestationType;
use App\Repository\RDVRepository;
use App\Repository\BijouxRepository;
use App\Repository\CategoryPrestationRepository;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryProduitsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    // Création de la route "Category Prestation"
    #[Route('/admin/category_prestations', name: 'admin_category_prestations')]
    public function category_prestations(CategoryPrestationRepository $ctgRepository): Response
    {
        $category_prestations = $ctgRepository->findAll();

        return $this->render('admin/prestations/category/read.html.twig', [
            'category_prestations' => $category_prestations
        ]);
    }

    // Création de la route "Création des Category Produits"
    #[Route('/admin/category_prestations/new', name: 'admin_create_category_prestations')]
    public function new_category_prestations(EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $ctg_prestations = new CategoryPrestation();
        $form = $this->createForm(CategoryPrestationType::class, $ctg_prestations);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManagerInterface->persist($ctg_prestations);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/prestation/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

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

    // ================================PRODUITS=================================

    // Création de la route "Category Produits"
    #[Route('/admin/category_produits', name: 'admin_category_produits')]
    public function category_produits(CategoryProduitsRepository $ctgRepository): Response
    {
        $ctg_produits = $ctgRepository->findAll();

        return $this->render('admin/produits/read.html.twig', [
            'ctg_produits' => $ctg_produits
        ]);
    }

    // Création de la route "Création des Category Produits"
    #[Route('/admin/category_produits/new', name: 'admin_create_category_produits')]
    public function new_category_produits(EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $ctg_produits = new CategoryProduits();
        $form = $this->createForm(CTGProduitsType::class, $ctg_produits);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManagerInterface->persist($ctg_produits);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/produits/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Création de la route "Produits"
    #[Route('/admin/produits', name: 'admin_produits')]
    public function produits(ProduitsRepository $produitsRepository, CategoryProduitsRepository $CTGProduitsRepository): Response
    {
        $produits = $produitsRepository->findAll();
        $categories = $CTGProduitsRepository->findAll(); // On recupère tous les categories_Produits qui servira à créer un selecteur pour pouvoir afficher que les produits 
        return $this->render('admin/produits/read.html.twig', [
            'produits' => $produits,
            'categories' => $categories, // On envoie les informations récupérer avec $CTGProduitsRepository->findAll() vers la vue twig avec comme variable "catégories"
        ]);
    }

    // Création de la route "Création de Produits"
    #[Route('/admin/produits/new', name: 'admin_create_produits')]
    public function new_produits(EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $slugger): Response
    {

        $produits = new Produits();
        $form = $this->createForm(ProduitsType::class, $produits);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $produitsfile = $form->get('img_produits')->getData();

            if ($produitsfile) {
                $originalFileName = pathinfo($produitsfile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $produitsfile->guessExtension();
            }

            try {
                $produitsfile->move(
                    $this->getParameter("image_produits_directory"),
                    $newFilename
                );
            } catch (FileException $e) {
            }

            $produits->setImgProduits($newFilename);

            $entityManagerInterface->persist($produits);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/produits/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    // Création de la route "Edit Produits"
    #[Route('/admin/produits/{id}/edit', name: 'admin_produits_edit')]
    public function edit_produits($id, ProduitsRepository $produitsRepository, EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $slugger): Response
    {
        $produits = $produitsRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(ProduitsType::class, $produits);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $produitsfile = $form->get('img_produits')->getData();
            // dd($produitsfile);
            if ($produitsfile) {
                $originalFileName = pathinfo($produitsfile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $produitsfile->guessExtension();

                try {
                    $produitsfile->move(
                        $this->getParameter("image_produits_directory"),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $produits->setImgProduits($newFilename);
            }

            $entityManagerInterface->persist($produits);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/produits/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    // Création de la route "Produits" pour supprimer
    #[Route('/admin/produits/{id}/delete', name: 'admin_produits_delete')]
    public function delete_produits($id, ProduitsRepository $produitsRepository): Response
    {
        $produits = $produitsRepository->findOneBy(['id' => $id]);
        $produitsRepository->remove($produits, true);
        return $this->redirectToRoute("admin_produits");
    }
    
// ==================================Calendar=======================================================
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
        return $this->render('admin/reservation/calendar.html.twig', compact('data'));
    }
    // =========================== BIJOUX ===========================
    // Création de la route "Bijoux"
    #[Route('/admin/bijoux', name: 'admin_bijoux')]
    public function bijoux(BijouxRepository $bijouxRepository): Response
    {
        $bijoux = $bijouxRepository->findAll();

        return $this->render('admin/bijoux/read.html.twig', [
            'bijoux' => $bijoux
        ]);
    }

    // Création de la route "Création de bijoux"
    #[Route('/admin/bijoux/new', name: 'admin_create_bijoux')]
    public function new_bijoux(EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $slugger): Response
    {

        $bijoux = new Bijoux();
        $form = $this->createForm(BijouxType::class, $bijoux);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère l'image donner et on lui met une variable 
            $bijouxfile = $form->get('url_image')->getData();

            if ($bijouxfile) {
                $originalFileName = pathinfo($bijouxfile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $bijouxfile->guessExtension();
            }
            try {
                $bijouxfile->move(
                    $this->getParameter("image_bijoux_directory"),
                    $newFilename
                );
            } catch (FileException $e) {
            }

            $bijoux->setUrlImage($newFilename);

            $entityManagerInterface->persist($bijoux);
            $entityManagerInterface->flush();
        }

        return $this->render('admin/produits/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/admin/calendar/{id}/edit', name: 'admin_calendar_edit')]
    public function majEvent($id, PrestationsRepository $prestationsRepository, Connection $connection, RDVRepository $rdvRepository, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        $rdv = $rdvRepository->findOneBy(['id' => $id]);

        $result = $connection->fetchAllAssociative('SELECT * FROM rdv_prestations WHERE rdv_id =' . $id);

        $form = $this->createFormBuilder($rdv)
            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder' => ' Votre nom'
                ]
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'placeholder' => ' Votre prenom'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => ' Votre email'
                ]
            ])
            ->add('tel', TelType::class, [
                'attr' => [
                    'placeholder' => ' Votre numero de téléphone'
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'label' => "Nom du Coiffeur "
            ])

            ->add('date_time', DateTimeType::class, [
                'label' => "Date du rendez-vous",
                'widget' => "single_text"
            ])

            ->add("submit", SubmitType::class, [
                'label' => "Valider",
                'attr' => ['class' => "button_valide_reservation"],
            ])

            ->getForm();

        for ($i = 0; $i < count($result); $i++) {
            $form->add("prestation$i", EntityType::class, [
                "class" => Prestations::class,
                'choice_label' => 'title',
                'empty_data' => '0',
                'mapped' => false,
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($rdv);
            $entityManagerInterface->flush();
        }
        return $this->render('admin/modifiy_reservation.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
