<?php

namespace App\Form;

use App\Entity\PaiementCommission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementCommissionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => "Date de l'opération",
                'widget' => 'single_text',
                'required' => false,
                'empty_data' => null
            ])
            ->add('montant', NumberType::class, [
                'label' => "Montant"
            ])
            ->add('refnotededebit', TextType::class, [
                'label' => "Référence / note de débit"
            ])
            ->add('description', TextType::class, [
                'label' => "Description"
            ])
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PaiementCommission::class,
        ]);
    }
}
