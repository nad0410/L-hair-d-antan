<?php

namespace App\Form;

use App\Entity\Prestations;
use App\Entity\RDV;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RDVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('email', EmailType::class)
        ->add('tel', TelType::class)
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
        ->add('date_time', DateTimeType::class, [
            'widget' => "single_text"
        ])
        ->add("submit", SubmitType::class, [
            'label' => "Valider"
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RDV::class,
        ]);
    }
}
