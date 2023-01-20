<?php


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MonnaieSearchType extends AbstractType
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
            ->add('islocale', ChoiceType::class, [
                'label' => "Nature",
                'required' => false,
                'expanded' => false,
                'placeholder' => 'Tous',
                'choices' => array(
                    'Monnaie étrangère' => 0,
                    'Monnaie locale' => 1
                ),
                'row_attr' => [
                    'class' => 'input-group'
                ]
            ]);
        //->add("Rechercher", SubmitType::class);
    }
}
