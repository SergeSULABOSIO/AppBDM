<?php

namespace App\Form;

use App\Entity\Entreprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepriseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('adresse')
            ->add('telephone')
            ->add('rccm')
            ->add('idnat')
            ->add('numimpot')
            ->add('secteur', ChoiceType::class, [
                'choices'  => [
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
                ]
            ])
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
