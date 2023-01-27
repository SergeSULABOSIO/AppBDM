<?php

namespace App\Form;

use DateTime;
use App\Entity\Taxe;
use App\Entity\Police;
use App\Entity\Monnaie;
use App\Entity\PaiementTaxe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PaiementTaxeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => "Date de l'opération",
                'widget' => 'single_text',
                'required' => false,
                'data' => new DateTime('now'),
                'empty_data' => null
            ])
            ->add('montant', NumberType::class, [
                'label' => "Montant"
            ])
            ->add('exercice', TextType::class, [
                'label' => "Exercice"
            ])
            ->add('refnotededebit', TextType::class, [
                'label' => "Référence / note de débit"
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
            ->add('police', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'class'  => Police::class,
                'label' => "Police"
            ])
            ->add('taxe', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'class'  => Taxe::class,
                'label' => "Taxe payée"
            ])
            ->add('Enregistrer', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PaiementTaxe::class,
        ]);
    }
}
