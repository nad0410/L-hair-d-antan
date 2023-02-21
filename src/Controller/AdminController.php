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
    // ============================================= CATEGORY PRESTATIONS ==========================================================================
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
            return $this->redirectToRoute('admin_category_prestations');
        }

        return $this->render('admin/prestations/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/category_prestations/{id}/edit', name: 'admin__category_prestations_edit')]
    public function edit_category_prestations($id, CategoryPrestationRepository $CTGPrestationsRepo, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $category_prestations = $CTGPrestationsRepo->findOneBy(['id' => $id]);
        $form = $this->createForm(CategoryPrestationType::class, $category_prestations);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($category_prestations);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('admin_category_prestations');
        }

        return $this->render('admin/prestations/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    // Création de la route "category produits" pour supprimer
    #[Route('/admin/category_produits/{id}/delete', name: 'admin_category_prestations_delete')]
    public function delete_category_prestations($id, CategoryPrestationRepository $CTGPrestationsRepo): Response
    {
        $prestation = $CTGPrestationsRepo->findOneBy(['id' => $id]);
        $CTGPrestationsRepo->remove($prestation, true);
        return $this->redirectToRoute('admin_category_prestations');
    }

    // ====================================================PRESTATIONS=========================================================================
    // Création de la route "Prestations"
    #[Route('/admin/prestations/{id}', name: 'admin_prestations')]
    public function prestations($id, PrestationsRepository $prestationsRepository, CategoryPrestationRepository $CTGPrestationsRepo): Response
    {
        $prestations = $prestationsRepository->findBy(['category' => $id]); // On recupère tous les produits ayant comme category l'id {id} 
        $categories = $CTGPrestationsRepo->findAll(); // On recupère tous les categories de prestation qui servira à créer un selecteur pour pouvoir afficher que certaine prestation

        return $this->render('admin/prestations/read.html.twig', [
            'prestations' => $prestations,
            "categories" => $categories
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
            return $this->redirectToRoute('admin_prestations', ['id' => 1]);
        }

        return $this->render('admin/prestations/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/prestation/{id}/edit', name: 'admin__prestation_edit')]
    public function edit_prestation($id, PrestationsRepository $_prestationRepository, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $bijoux = $_prestationRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(PrestationsType::class, $bijoux);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($bijoux);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('admin_prestations', ['id' => 1]);
        }

        return $this->render('admin/prestations/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    // Création de la route "prestation" pour supprimer
    #[Route('/admin/prestation/{id}/delete', name: 'admin_prestation_delete')]
    public function delete_prestation($id, PrestationsRepository $prestationRepository): Response
    {
        $prestation = $prestationRepository->findOneBy(['id' => $id]);
        $prestationRepository->remove($prestation, true);
        return $this->redirectToRoute('admin_prestations', ['id' => 1]);
    }

    // ====================================================Category Produits=========================================================================

    // Création de la route "Category Produits"
    #[Route('/admin/category_produits', name: 'admin_category_produits')]
    public function category_produits(CategoryProduitsRepository $ctgRepository): Response
    {
        $ctg_produits = $ctgRepository->findAll();

        return $this->render('admin/produits/category/read.html.twig', [
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
            return $this->redirectToRoute('admin_category_produits');
        }

        return $this->render('admin/produits/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/category_produits/{id}/edit', name: 'admin__category_produits_edit')]
    public function edit_category_produits($id, CategoryProduitsRepository $CTGProduitsRepository, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $category_produits = $CTGProduitsRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(CTGProduitsType::class, $category_produits);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($category_produits);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('admin_category_produits');
        }

        return $this->render('admin/produits/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    // Création de la route "category produits" pour supprimer
    #[Route('/admin/category_produits/{id}/delete', name: 'admin_category_produits_delete')]
    public function delete_category_produits($id, CategoryProduitsRepository $CTGProduitsRepository): Response
    {
        $prestation = $CTGProduitsRepository->findOneBy(['id' => $id]);
        $CTGProduitsRepository->remove($prestation, true);
        return $this->redirectToRoute('admin_category_produits');
    }

    // ====================================================PRODUITS=========================================================================

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

            // this condition is needed because the 'brochure' field is not required
            // On as besoin de cette condition parceque le champ 'produit' n'est pas requis

            // so the PDF file must be processed only when a file is uploaded
            // donc l'image doit être traité que lorsque le fichier uploader.
            if ($produitsfile) {
                $originalFileName = pathinfo($produitsfile->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL
                // Cela est nécessaire pour inculure en sécurité le nom du fichier comme une parti de l'URL
                $safeFilename = $slugger->slug($originalFileName);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $produitsfile->guessExtension();
            }
            // Move the file to the directory where brochures are stored
            // Déplacer le fichier dans le repertoire des images stocker
            try {
                $produitsfile->move(
                    $this->getParameter("image_produits_directory"),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                // ... récupère les exceptions si quelque chose arriver lors du televersement du fichier ( image )

            }

            // updates the 'brochureFilename' property to store the PDF file name
            // Met a jours la propriété 'url_image' pour stocker le nom du fichier

            // instead of its contents
            // Au lieu de son contenu
            $produits->setImgProduits($newFilename);

            // ... persist the $product variable or any other work
            // ... Persister la vartiable $product ou tout autre travail
            $entityManagerInterface->persist($produits);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_produits');
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
            return $this->redirectToRoute('admin_produits');
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
            return $this->redirectToRoute('admin_bijoux');
        }

        return $this->render('admin/bijoux/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/admin/bijoux/{id}/edit', name: 'admin_bijoux_edit')]
    public function edit_bijoux($id, BijouxRepository $bijouxRepository, EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $slugger): Response
    {
        $bijoux = $bijouxRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(BijouxType::class, $bijoux);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bijouxfile = $form->get('url_image')->getData();
            // dd($produitsfile);
            if ($bijouxfile) {
                $originalFileName = pathinfo($bijouxfile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFilename = $safeFilename . "-" . uniqid() . "." . $bijouxfile->guessExtension();

                try {
                    $bijouxfile->move(
                        $this->getParameter("image_bijoux_directory"),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $bijoux->setUrlImage($newFilename);
            }

            $entityManagerInterface->persist($bijoux);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('admin_bijoux');
        }

        return $this->render('admin/bijoux/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    // Création de la route "Bijoux" pour supprimer
    #[Route('/admin/bijoux/{id}/delete', name: 'admin_bijoux_delete')]
    public function delete_bijoux($id, BijouxRepository $bijouxRepository): Response
    {
        $bijoux = $bijouxRepository->findOneBy(['id' => $id]);
        $bijouxRepository->remove($bijoux, true);
        return $this->redirectToRoute("admin_bijoux");
    }
}
