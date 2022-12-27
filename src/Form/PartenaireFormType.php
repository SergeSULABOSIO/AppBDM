<?php

namespace App\Form;

use App\Entity\Partenaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartenaireFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom du Partenaire / Intermédiaire"
            ])
            ->add('part', NumberType::class, [
                'label' => "Part (en %)"
            ])
            ->add('adresse', TextType::class, [
                'label' => "Adresse physique"
            ])
            ->add('email', EmailType::class, [
                'label' => "Email"
            ])
            ->add('siteweb', UrlType::class, [
                'label' => "Site internet"
            ])
            ->add('rccm', TextType::class, [
                'label' => "N° RCCM"
            ])
            ->add('idnat', TextType::class, [
                'label' => "Id. Nationale"
            ])
            ->add('numimpot', TextType::class, [
                'label' => "N° Impôt"
            ])
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
