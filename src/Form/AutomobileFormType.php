<?php

namespace App\Form;

use App\Entity\Automobile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutomobileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plaque')
            ->add('chassis')
            ->add('model')
            ->add('marque')
            ->add('annee')
            ->add('puissance')
            ->add('valeur')
            ->add('nbsieges')
            ->add('utilite')
            ->add('nature')
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Automobile::class,
        ]);
    }
}
