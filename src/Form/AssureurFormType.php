<?php

namespace App\Form;

use App\Entity\Assureur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssureurFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom de l'assureur"
            ])
            ->add('adresse', TextType::class, [
                'label' => "Adresse physique"
            ])
            ->add('telephone', TextType::class, [
                'label' => "Téléphone"
            ])
            ->add('email', EmailType::class, [
                'label' => "Adresse mail"
            ])
            ->add('siteweb', UrlType::class, [
                'label' => "Site Internet"
            ])
            ->add('rccm', TextType::class, [
                'label' => "RCCM"
            ])
            ->add('idnat', TextType::class, [
                'label' => "Id. Nationale"
            ])
            ->add('licence', TextType::class, [
                'label' => "N° de Licence / Autorisation"
            ])
            ->add('numimpot', TextType::class, [
                'label' => "N° Impôt"
            ])
            ->add('isreassureur', ChoiceType::class, [
                'label' => "Est-il un réassureur?",
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
            'data_class' => Assureur::class,
        ]);
    }
}
