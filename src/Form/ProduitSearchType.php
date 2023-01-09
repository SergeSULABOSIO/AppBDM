<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProduitSearchType extends AbstractType
{

    const CATEGORIES = [
        "Tous" => -1,
        'Assurances des Responsabilités' => 0,
        'Assurances des Biens' => 1,
        'Assurances vie' => 2
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
            ->add('categorie', ChoiceType::class, [
                'label' => "Catégorie",
                'required' => true,
                'expanded' => false,
                'choices' => self::CATEGORIES,
                'row_attr' => [
                    'class' => 'input-group',
                ]
            ]);
        //->add("Rechercher", SubmitType::class);
    }
}
