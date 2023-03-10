<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\CategoryProduits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('marque', TextType::class, [
                'label' => 'Marque',
            ])
            
            ->add('prix', NumberType::class)
            ->add('img_produits', FileType::class, [
                'label' => 'Image Produits',

                'mapped' => false,

                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => CategoryProduits::class,
                "choice_label" => "name"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
