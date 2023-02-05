<?php

namespace App\Controller\Admin;

use App\Entity\Bijoux;
use App\Form\BijouxType;
use App\Repository\BijouxRepository;
use Symfony\Component\Routing\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class AdminController extends AbstractController
{


    // =========================== BIJOUX ===========================
    // Création de la route "Produits"
    #[Route('/admin/bijoux', name: 'admin_produits')]
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
}
