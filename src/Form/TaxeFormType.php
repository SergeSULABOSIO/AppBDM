<?php

namespace App\Form;

use App\Entity\Taxe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaxeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom de la Taxe"
            ])
            ->add('description', TextType::class, [
                'label' => "Brève description"
            ])
            ->add('taux', NumberType::class, [
                'label' => "Taux d'imposition (en %)"
            ])
            ->add('payableparcourtier', ChoiceType::class, [
                'label' => "Est-elle à la charge du courtier?",
                'required' => true,
                'expanded' => false,
                'choices' => array(
                    'Non' => false,
                    'Oui' => true
                )
            ])
            ->add('organisation', TextType::class, [
                'label' => "Oragisation"
            ])
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Taxe::class,
        ]);
    }
}
