<?php

namespace App\Form;

use App\Entity\Police;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PoliceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateoperation')
            ->add('dateemission')
            ->add('dateeffet')
            ->add('dateexpiration')
            ->add('reference')
            ->add('idavenant')
            ->add('typeavenant')
            ->add('capital')
            ->add('primenette')
            ->add('fronting')
            ->add('arca')
            ->add('tva')
            ->add('fraisadmin')
            ->add('primetotale')
            ->add('discount')
            ->add('modepaiement')
            ->add('ricom')
            ->add('localcom')
            ->add('frontingcom')
            ->add('remarques')
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Police::class,
        ]);
    }
}
