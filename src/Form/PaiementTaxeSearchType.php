<?php

use App\Entity\Taxe;
use App\Entity\Police;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class PaiementTaxeSearchType extends AbstractType
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
            ->add('police', EntityType::class, [
                'label' => "Police",
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Tous',
                'class'  => Police::class,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('taxe', EntityType::class, [
                'label' => "Taxe",
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'placeholder' => 'Tous',
                'class'  => Taxe::class,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add("motcle", TextType::class, [
                'label' => "Mot clé",
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group',
                ]
            ]);
        //->add("Rechercher", SubmitType::class);
    }
}
