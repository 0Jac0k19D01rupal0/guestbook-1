<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('sort', ChoiceType::class, [
                'choices' => [
                    'Recently ↓' => 'id.DESC',
                    'Latest ↑' => 'id.ASC',
                    'Username A-Z ↓' => 'username.ASC',
                    'Username Z-A ↑' => 'username.DESC',
                    'Email A-Z ↓' => 'email.ASC',
                    'Email Z-A ↑' => 'email.DESC'
                ],
                'attr' => ['class' => 'form-control message-sort', 'onchange' => 'if(this.value != 0) { this.form.submit(); }']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
