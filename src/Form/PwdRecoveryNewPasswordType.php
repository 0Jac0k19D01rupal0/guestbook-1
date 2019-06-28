<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PwdRecoveryNewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'auth.password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Password',
                        'id' => 'inputPassword'
                    ]
                ],
                'second_options' => [
                    'label' => 'auth.repeat_password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Repeat Password',
                        'id' => 'inputPassword'
                    ]
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
