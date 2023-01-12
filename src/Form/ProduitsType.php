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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('marque')
            ->add('prix')
            ->add('description')
            ->add('img_produits', FileType::class, [
                'label' => 'Image Produits',

                'mapped' => false,

                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => CategoryProduits::class,
                "choice_label" => "name"
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
