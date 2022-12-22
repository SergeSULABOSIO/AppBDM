<?php

namespace App\Form;

use App\Entity\Partenaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartenaireFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('part')
            ->add('adresse')
            ->add('email')
            ->add('siteweb')
            ->add('rccm')
            ->add('idnat')
            ->add('numimpot')
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partenaire::class,
        ]);
    }
}
