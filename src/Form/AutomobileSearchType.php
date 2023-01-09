<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AutomobileSearchType extends AbstractType
{

    const NATURES = [
        "Tous" => -1,
        'Voiture' => 0,
        'Jeep / Pick-up' => 1,
        'Mini-Bus' => 2,
        'Bus' => 3,
        'Tracteur' => 4,
        'Remorque' => 5,
        'Tracteur + remorque' => 6
    ];

    const UTILITES = [
        "Tous" => -1,
        'Transport privé' => 0,
        'Transport du personnel' => 1,
        'Taxi / Transport en commun' => 2,
        'Transport des marchandise' => 3,
        'Engin de chantier' => 4
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
            ->add("nature", ChoiceType::class, [
                'label' => "Nature",
                'choices' => self::NATURES,
                'row_attr' => [
                    'class' => 'input-group',
                ]
            ])
            ->add("utilite", ChoiceType::class, [
                'label' => "Usage",
                'choices' => self::UTILITES,
                'row_attr' => [
                    'class' => 'input-group',
                ]
            ]);
        //->add("Rechercher", SubmitType::class);
    }
}
