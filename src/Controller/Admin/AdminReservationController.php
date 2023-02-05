<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\User;
use App\Entity\Prestations;
use Doctrine\DBAL\Connection;
use App\Repository\RDVRepository;
use Symfony\Component\Routing\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminController extends AbstractController
{
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
