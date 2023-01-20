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
                "class" => Prestations::class,
                'choice_label' => 'title',
                'empty_data' => '0',
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
                'label' => "Date du rendez-vous",
                'widget' => "single_text"
            ])


            ->add("check_cgu", CheckboxType::class, [
                'label' => "Valider",
                'attr' => ['class' => "check_valide_cgu"],
                'mapped' => false,
            ])

            ->add("captcha", ReCaptchaType::class)

            ->add("submit", SubmitType::class, [
                'label' => "Valider",
                'attr' => ['class' => "button_valide_reservation"],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RDV::class,
        ]);
    }
}
