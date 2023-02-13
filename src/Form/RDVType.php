<?php

namespace App\Form;

use App\Entity\Prestations;
use App\Entity\RDV;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;

class RDVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add("prestation", EntityType::class, [
                // On définit sur quelle Object/Class il doit récupérer les valeurs
                "class" => Prestations::class,
                // On définit quelles valeurs il doit afficher dans le sélecteur
                'choice_label' => 'title',
                // On definit quelle valeur il auras si rien n'est choisi (par sécurité)
                'empty_data' => '0',
                // On demande à Symfony de ne pas le traiter automatiquement
                'mapped' => false,
            ])
            ->add("prestation2", EntityType::class, [
                "class" => Prestations::class,
                'choice_label' => 'title',
                'required' => false,
                'empty_data' => '',
                'mapped' => false,
                'attr' => ['class' => "hidden"],
            ])
            ->add("prestation3", EntityType::class, [
                "class" => Prestations::class,
                'choice_label' => 'title',
                'required' => false,
                'empty_data' => '',
                'mapped' => false,
                'attr' => ['class' => "hidden"],
            ])

            ->add('date_time', DateTimeType::class, [
                // On change son label
                'label' => "Date du rendez-vous",
                // On lui dit de s'afficher comme un calendrier
                'widget' => "single_text"
            ])

            ->add("check_cgu", CheckboxType::class, [
                'label' => "Valider",
                // On lui définit une classe
                'attr' => ['class' => "check_valide_cgu"],
                // On demande à Symfony de ne pas le traiter car je ne vais rien faire avec cette valeurs
                'mapped' => false,
            ])

            ->add("captcha", ReCaptchaType::class, ["type" => "invisible"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RDV::class,
        ]);
    }
}
