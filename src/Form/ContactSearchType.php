<?php

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("motcle", TextType::class, [
                'label' => "Mot Clé",
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ])
            ->add('client', EntityType::class, [
                'label' => "Client",
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'class'  => Client::class,
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ]);
        //->add("Rechercher", SubmitType::class);
    }
}
