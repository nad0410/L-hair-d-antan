<?php

namespace App\Controller;

use App\Entity\CategoryProduits;
use DateTime;
use App\Form\RDVType;
use App\Entity\Prestations;
use App\Entity\Produits;
use App\Entity\RDV;
use App\Form\CTGProduitsType;
use App\Form\PrestationsType;
use App\Form\ProduitsType;
use App\Repository\CategoryProduitsRepository;
use App\Repository\RDVRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationsRepository;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function majEvent($id, PrestationsRepository $prestationsRepository, RDVRepository $rdvRepository, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        $getrdv = $rdvRepository->getRdvPrestation($id);

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
    public function produits(ProduitsRepository $produitsRepository): Response
    {
        $produits = $produitsRepository->findAll();

        return $this->render('admin/produits/read.html.twig', [
            'produits' => $produits
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
    public function edit_produits($id,ProduitsRepository $produitsRepository, EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $slugger): Response
    {
        $produits = $produitsRepository->findOneBy(['id' => $id]);
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
        // Création de la route "Produits"
        #[Route('/admin/produits/{id}/delete', name: 'admin_produits_delete')]
        public function delete_produits($id,ProduitsRepository $produitsRepository): Response
        {
            $produits = $produitsRepository->findOneBy(['id' => $id]);
            $produitsRepository->remove($produits,true);
            return $this->redirectToRoute("admin_produits");
        }
}
