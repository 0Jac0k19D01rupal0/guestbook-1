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
                    'forms.sort_id_desc' => 'id.DESC',
                    'forms.sort_id_asc' => 'id.ASC',
                    'forms.sort_username_desc' => 'username.ASC',
                    'forms.sort_username_asc' => 'username.DESC',
                    'forms.sort_email_desc' => 'email.ASC',
                    'forms.sort_email_asc' => 'email.DESC'
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
