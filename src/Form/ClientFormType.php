<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom du client"
            ])
            ->add('secteur', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ],
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
            ->add('adresse', TextType::class, [
                'label' => "Adresse physique"
            ])
            ->add('telephone', TextType::class, [
                'label' => "N° de téléphone"
            ])
            ->add('email', EmailType::class, [
                'label' => "Adresse mail"
            ])
            ->add('siteweb', UrlType::class, [
                'label' => "Site Internet"
            ])
            ->add('ispersonnemorale', ChoiceType::class, [
                'label' => "Est-il une personne morale?",
                'required' => true,
                'expanded' => false,
                'choices' => array(
                    'Non' => false,
                    'Oui' => true
                )
            ])
            ->add('rccm', TextType::class, [
                'label' => "N° RCCM"
            ])
            ->add('idnat', TextType::class, [
                'label' => "Id. Nationale"
            ])
            ->add('numipot', TextType::class, [
                'label' => "N° Impôt"
            ])
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
