<?php

use App\Entity\Assureur;
use DateTime;
use App\Entity\Taxe;
use App\Entity\Client;
use App\Entity\Police;
use App\Entity\Partenaire;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class PoliceSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateA', DateType::class, [
                'label' => "Entre le",
                'widget' => 'single_text',
                'required' => true,
                'empty_data' => null,
                //'data' => new DateTime('now'),
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('dateB', DateType::class, [
                'label' => "Et le",
                'widget' => 'single_text',
                'required' => true,
                'empty_data' => null,
                //'data' => new DateTime('now'),
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('produit', EntityType::class, [
                'label' => "Produit",
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Tous',
                'class'  => Produit::class,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('client', EntityType::class, [
                'label' => "Client",
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Tous',
                'class'  => Client::class,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('partenaire', EntityType::class, [
                'label' => "Partenaire",
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Tous',
                'class'  => Partenaire::class,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('assureur', EntityType::class, [
                'label' => "Assureur",
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Tous',
                'class'  => Assureur::class,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('taxe', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Tous',
                'row_attr' => [
                    'class' => 'input-group'
                ],
                'class'  => Taxe::class,
                'label' => "Taxe"
            ])
            ->add("motcle", TextType::class, [
                'label' => "Mot clé",
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ]);
        //->add("Rechercher", SubmitType::class);
    }
}
