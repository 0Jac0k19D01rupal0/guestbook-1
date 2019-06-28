<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PwdRecoveryEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'auth.email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                    'id' => 'inputEmail'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
