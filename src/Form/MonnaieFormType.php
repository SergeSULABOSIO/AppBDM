<?php

namespace App\Form;

use App\Entity\Monnaie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MonnaieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom de la monnaie"
            ])
            ->add('code', TextType::class, [
                'label' => "Code / Symbole"
            ])
            ->add('tauxusd', NumberType::class, [
                'label' => "Taux de change en M. Locale"
            ])
            ->add('islocale', ChoiceType::class, [
                'label' => "Est-elle une monnaie locale?",
                'required' => true,
                'expanded' => false,
                'choices' => array(
                    'Non' => false,
                    'Oui' => true
                )
            ])
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Monnaie::class,
        ]);
    }
}
