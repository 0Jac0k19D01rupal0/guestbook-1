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

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Username']
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'E-mail']
            ])
            ->add('homepage', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Your Homepage'],
                'required' => false
            ])
            ->add('text', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => '5']
            ])
            ->add('picture', FileType::class, [
                'label' => 'Available download formats: .png .jpg .jpeg .tiff .webp',
                'attr' => ['class' => 'form-control-file']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
