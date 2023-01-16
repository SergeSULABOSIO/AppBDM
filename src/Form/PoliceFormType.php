<?php

namespace App\Form;

use DateTime;
use App\Entity\Client;
use App\Entity\Police;
use App\Entity\Monnaie;
use App\Entity\Produit;
use App\Entity\Assureur;
use App\Entity\Entreprise;
use App\Entity\Partenaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PoliceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateoperation', DateType::class, [
                'label' => "Date de l'opération",
                'widget' => 'single_text',
                'required' => false,
                'data' => new DateTime('now'),
                'empty_data' => null
            ])
            ->add('dateemission', DateType::class, [
                'label' => "Date d'émission",
                'widget' => 'single_text',
                'required' => false,
                'data' => new DateTime('now'),
                'empty_data' => null
            ])
            ->add('dateeffet', DateType::class, [
                'label' => "Date d'effet",
                'widget' => 'single_text',
                'required' => false,
                'data' => new DateTime('now'),
                'empty_data' => null
            ])
            ->add('dateexpiration', DateType::class, [
                'label' => "Date d'expiration",
                'widget' => 'single_text',
                'required' => false,
                'data' => new DateTime('+364 day'),//new DateTime("+364 day")
                'empty_data' => null
            ])
            ->add('reference', TextType::class, [
                'label' => "Référence de la police"
            ])
            ->add('idavenant', NumberType::class, [
                'label' => "Id de l'avenant"
            ])
            ->add('typeavenant', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'choices'  => [
                    "SOUSCRIPTION" => 0,
                    "RENOUVELLEMENT" => 1,
                    "ANNULATION" => 2,
                    "RESILIATION" => 3,
                    "RISTOURNE" => 4,
                    "PROROGATION" => 5
                ],
                'label' => "Type d'avenant"
            ])
            ->add('capital', NumberType::class, [
                'label' => "Capital / Limte d'indemnisation"
            ])
            ->add('primenette', NumberType::class, [
                'label' => "Prime nette (HT)"
            ])
            ->add('fronting', NumberType::class, [
                'label' => "Frais Fronting"
            ])
            ->add('arca', NumberType::class, [
                'label' => "Frais ARCA (2%)"
            ])
            ->add('tva', NumberType::class, [
                'label' => "TVA (16%)"
            ])
            ->add('fraisadmin', NumberType::class, [
                'label' => "Frais Admin."
            ])
            ->add('primetotale', NumberType::class, [
                'label' => "Prime totale (TTC)"
            ])
            ->add('discount', NumberType::class, [
                'label' => "Rabais"
            ])
            ->add('commissionpartageable', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'choices'  => [
                    "Toutes les commissions" => 0,
                    "La commission de reassurance" => 1,
                    "La commission locale / Arca" => 2,
                    "La commission sur le Fronting" => 3,
                    "Aucune commission" => 4
                ],
                'label' => "Quelle commission partager?"
            ])
            ->add('modepaiement', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'choices'  => [
                    "Paiement Annuel" => 0,
                    "Paiement Semestriel" => 1,
                    "Paiement Trimestriel" => 2
                ],
                'label' => "Mode de Paiement"
            ])
            ->add('ricom', NumberType::class, [
                'label' => "Commission (ht) / Réassurance"
            ])
            ->add('localcom', NumberType::class, [
                'label' => "Commission (ht) / Arca"
            ])
            ->add('frontingcom', NumberType::class, [
                'label' => "Commission (ht) / Fronting"
            ])
            ->add('remarques', TextareaType::class, [
                'label' => "Rémarques",
                'required' => false
            ])
            ->add('reassureurs', TextType::class, [
                'label' => "Réassureurs",
                'required' => false
            ])
            ->add('entreprise')
            ->add('monnaie', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'class'  => Monnaie::class,
                'label' => "Monnaie"
            ])
            ->add('client', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'class'  => Client::class,
                'label' => "Client"
            ])
            ->add('produit', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'class'  => Produit::class,
                'label' => "Couverture"
            ])
            ->add('partenaire', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ],
                'class'  => Partenaire::class,
                'label' => "Partenaire"
            ])
            ->add('assureurs', EntityType::class, [
                'expanded' => false,
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'class'  => Assureur::class,
                'label' => "Assureurs"
            ])
            ->add('Enregistrer', SubmitType::class);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Police::class,
        ]);
    }
}
