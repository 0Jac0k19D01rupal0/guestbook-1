<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (is_null($options['userdata'])) {
            $builder
                ->add('username', TextType::class, [
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Username']
                ])
                ->add('email', EmailType::class, [
                    'attr' => ['class' => 'form-control', 'placeholder' => 'E-mail']
                ])
            ;
        }
        else {
            $builder
                ->add('username', TextType::class, [
                    'attr' => ['class' => 'form-control', 'placeholder' => 'forms.message_username', 'readonly' => 'readonly'],
                    'data' => $options['userdata']->getUsername()
                ])
                ->add('email', EmailType::class, [
                    'attr' => ['class' => 'form-control', 'placeholder' => 'forms.message_email', 'readonly' => 'readonly'],
                    'data' => $options['userdata']->getEmail()
                ])
            ;
        }
        $builder
            ->add('homepage', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'forms.message_homepage'],
                'required' => false
            ])
            ->add('text', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => '5']
            ])
            ->add('picture', FileType::class, [
                'label' => 'forms.message_file_type',
                'attr' => ['class' => 'form-control-file'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
        $resolver->setRequired(['userdata']);
    }
}
