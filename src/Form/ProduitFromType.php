<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ProduitFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom du produit / Risque"
            ])
            ->add('description', TextType::class, [
                'label' => "Brève description"
            ])
            ->add('tauxarca', NumberType::class, [
                'label' => "Taux / Commission de courtage (%)"
            ])
            ->add('isobligatoire', ChoiceType::class, [
                'label' => "Est-elle une assurance obligatoire?",
                'required' => true,
                'expanded' => false,
                'choices' => array(
                    'Non' => false,
                    'Oui' => true
                )
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => "Catégorie d'assurance",
                'required' => true,
                'expanded' => false,
                'choices' => array(
                    'Assurances des Responsabilités' => 0,
                    'Assurances des Biens' => 1,
                    'Assurances vie' => 2
                )
            ])
            ->add('isabonnement', ChoiceType::class, [
                'label' => "Est-elle une assurance abonnement?",
                'required' => true,
                'expanded' => false,
                'choices' => array(
                    'Non' => false,
                    'Oui' => true
                )
            ])
            ->add('entreprise')
            ->add('Enregistrer', SubmitType::class);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
