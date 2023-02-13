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
                'empty_data' => null,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('dateemission', DateType::class, [
                'label' => "Date d'émission",
                'widget' => 'single_text',
                'required' => false,
                'data' => new DateTime('now'),
                'empty_data' => null,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('dateeffet', DateType::class, [
                'label' => "Date d'effet",
                'widget' => 'single_text',
                'required' => false,
                'data' => new DateTime('now'),
                'empty_data' => null,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('dateexpiration', DateType::class, [
                'label' => "Echéance",
                'widget' => 'single_text',
                'required' => false,
                'data' => new DateTime('+364 day'), //new DateTime("+364 day")
                'empty_data' => null,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('reference', TextType::class, [
                'label' => "Référence",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('idavenant', NumberType::class, [
                'label' => "Id. Avenant",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('typeavenant', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'choices'  => [
                    "SOUSCRIPTION" => 0,
                    "RENOUVELLEMENT" => 1,
                    "ANNULATION" => 2,
                    "RESILIATION" => 3,
                    "RISTOURNE" => 4,
                    "PROROGATION" => 5
                ],
                'label' => "Type d'avenant",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('capital', NumberType::class, [
                'label' => "Capital / Limte",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('primenette', NumberType::class, [
                'label' => "Prime nette (HT)",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('fronting', NumberType::class, [
                'label' => "Fronting",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('arca', NumberType::class, [
                'label' => "Arca (2%)",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('tva', NumberType::class, [
                'label' => "TVA (16%)",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('fraisadmin', NumberType::class, [
                'label' => "Frais Admin.",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('primetotale', NumberType::class, [
                'label' => "Prime (TTC)",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('discount', NumberType::class, [
                'label' => "Rabais",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('modepaiement', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'choices'  => [
                    "Annuel" => 0,
                    "Semestriel" => 1,
                    "Trimestriel" => 2
                ],
                'label' => "Mode de Paiement",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //Commissions - RI
            ->add('ricom', NumberType::class, [
                'label' => "Com. Réassurance (ht)",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //Commissions - Local
            ->add('localcom', NumberType::class, [
                'label' => "Com. Ordinaire (ht)",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //Commissions - Fronting
            ->add('frontingcom', NumberType::class, [
                'label' => "Com. Fronting (ht)",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //ri com - can share
            ->add('cansharericom', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'choices'  => [
                    "Non" => false,
                    "Oui" => true
                ],
                'label' => "Partageable?",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //local com - can share
            ->add('cansharelocalcom', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'choices'  => [
                    "Non" => false,
                    "Oui" => true
                ],
                'label' => "Partageable?",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //fronting com - can share
            ->add('cansharefrontingcom', ChoiceType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'choices'  => [
                    "Non" => false,
                    "Oui" => true
                ],
                'label' => "Partageable?",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //ri com - payable by
            ->add('ricompayableby', TextType::class, [
                'label' => "Débiteur",
                'required' => true,
                'data' => "Le client lui-même",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //local com - payable by
            ->add('localcompayableby', TextType::class, [
                'label' => "Débiteur",
                'required' => true,
                'data' => "Le client lui-même",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // //fronting com - payable by
            ->add('frontingcompayableby', TextType::class, [
                'label' => "Débiteur",
                'required' => true,
                'data' => "Le client lui-même",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('remarques', TextType::class, [
                'label' => "Rémarques",
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('reassureurs', TextType::class, [
                'label' => "Réassureurs",
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('entreprise', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'class'  => Entreprise::class,
                'label' => "Entreprise",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('monnaie', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'class'  => Monnaie::class,
                'label' => "Monnaie",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('client', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'class'  => Client::class,
                'label' => "Client",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('produit', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'class'  => Produit::class,
                'label' => "Couverture",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('partenaire', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'class'  => Partenaire::class,
                'label' => "Partenaire, bénéficiaire de la retro-commission",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('assureur', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                // 'attr' => [
                //     'class' => 'select2'
                // ],
                'class'  => Assureur::class,
                'label' => "Assureur",
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            // ->add('assureurs', EntityType::class, [
            //     'expanded' => false,
            //     'multiple' => true,
            //     'required' => true,
            //     'attr' => [
            //         'class' => 'select2'
            //     ],
            //     'class'  => Assureur::class,
            //     'label' => "Assureurs",
            //     // 'row_attr' => [
            //     //     'class' => 'input-group'
            //     // ]
            // ])
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Police::class,
        ]);
    }
}
