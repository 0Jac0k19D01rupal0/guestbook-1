<?php

namespace App\Form;

use App\Entity\Message;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
                'data' => $options['picture'] ?? null,
                'required' => false
            ])
            ->add('text', CKEditorType::class, array(
                'attr' => [
                    'id' => 'record-input'
                ],
                'required' => false,
                'label' => 'record.data-info',
                'config' => [
                    'uiColor' => '#ffffff',
                    'toolbarGroups' => [
                        'clipboard',
                        ['editing', 'groups' => [ 'find', 'selection', 'spellchecker' ]],
                        'insert',
                        'links',
                        'tools',
                        ['document', 'groups' => [ 'mode', 'document', 'doctools' ]],
                        'uploadFile',
                        'others',
                        'sourcearea',
                        '/',
                        ['basicstyles', 'groups' => [ 'basicstyles', 'cleanup' ]],
                        'paragraph',
                        'styles',
                        'colors',
                        'about',
                    ],
                    'removeButtons' => 'Save,NewPage,Preview,Cut,Copy,Paste,Undo,Redo,Anchor,Underline,Strike,Superscript,Scayt,Flash,Image,About,Table',
                    'removeDialogTabs' => 'image:advanced;link:advanced;',
                    'fillEmptyBlocks' => false,
                    'basicEntities' => false,
                ],
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
        $resolver->setRequired(['userdata', 'picture']);
    }
}
