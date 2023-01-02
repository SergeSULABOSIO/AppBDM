<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ClientSearchType extends AbstractType
{
    pulbic function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add("motcle");
    }
}