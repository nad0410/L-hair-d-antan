<?php

namespace App\Controller\Admin;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Form\CTGProduitsType;
use App\Entity\CategoryProduits;
use Symfony\Component\Routing\Route;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryProduitsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class AdminController extends AbstractController
{
    
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

}

