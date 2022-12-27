<?php

namespace App\Form;

use App\Entity\Automobile;
use App\Entity\Monnaie;
use App\Entity\Police;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutomobileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plaque', TextType::class, [
                'label' => "N° de la plaque"
            ])
            ->add('chassis', TextType::class, [
                'label' => "N° de chassis"
            ])
            ->add('model', TextType::class, [
                'label' => "Modèle"
            ])
            ->add('marque', TextType::class, [
                'label' => "Marque"
            ])
            ->add('annee', TextType::class, [
                'label' => "Année de mise en circ."
            ])
            ->add('puissance', TextType::class, [
                'label' => "Puissance fiscale"
            ])
            ->add('valeur', NumberType::class, [
                'label' => "Valeur"
            ])
            ->add('nbsieges', NumberType::class, [
                'label' => "Nombre des sièges"
            ])
            ->add('utilite', ChoiceType::class, [
                'label' => "Quelle est son usage?",
                'required' => true,
                'expanded' => false,
                'choices' => array(
                    'Transport privé' => 0,
                    'Transport du personnel' => 1,
                    'Taxi / Transport en commun' => 2,
                    'Transport des marchandise' => 3,
                    'Engin de chantier' => 4
                )
            ])
            ->add('nature', ChoiceType::class, [
                'label' => "Quelle est sa nature?",
                'required' => true,
                'expanded' => false,
                'choices' => array(
                    'Voiture' => 0,
                    'Jeep / Pick-up' => 1,
                    'Mini-Bus' => 2,
                    'Bus' => 3,
                    'Tracteur' => 4,
                    'Remorque' => 5,
                    'Tracteur + remorque' => 6
                )
            ])
            ->add('polices', EntityType::class, [
                'expanded' => false,
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'class'  => Police::class,
                'label' => "Police d'assurance"
            ])
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
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Automobile::class,
        ]);
    }
}
