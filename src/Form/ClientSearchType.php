<?php

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ClientSearchType extends AbstractType
{

    const SECTEUR = [
        "Tous" => -1,
        "Administrations publiques" => 0,
        "Agriculture, foresterie, pêche et chasse" => 1,
        "Assurances" => 2,
        "Banques et Institutions financières" => 3,
        "Commerce et Distrbution" => 4,
        "Construction et Travaux Publics" => 5,
        "Enseignement" => 6,
        "Immobilier" => 7,
        "Importation" => 8,
        "Industrie de transformation" => 9,
        "Internet et Télécommunication" => 10,
        "Média, Arts, spectacles et loisirs" => 11,
        "Mines et autres extractions" => 12,
        "ONG" => 13,
        "Recherche Scientifique" => 14,
        "Restaurants et débits de boisson" => 15,
        "Santé et Assistance Sociale" => 16,
        "Sécurité et Gardienage" => 17,
        "Transport" => 18,
        "Autres" => 19
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("motcle", TextType::class, [
                'label' => "Mot Clé",
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group',
                ]
            ])
            ->add("secteur", ChoiceType::class, [
                'label' => "Secteur",
                'choices' => self::SECTEUR,
                'row_attr' => [
                    'class' => 'input-group',
                ]
            ]);
        //->add("Rechercher", SubmitType::class);
    }
}
