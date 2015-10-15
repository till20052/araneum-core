<?php

namespace Araneum\Bundle\MailBundle\Form\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('target', null, ['required' => true])
            ->add('sender', null, ['required' => true])
            ->add('headline', null, ['required' => true])
            ->add(
                'text_body',
                null,
                [
                    'mapped' => 'textBody',
                    'required' => true
                ]
            )
            ->add(
                'html_body',
                null,
                [
                    'mapped' => 'htmlBody',
                    'required' => true
                ]
            )
            ->add('attachment');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Araneum\Bundle\MailBundle\Entity\Mail']);
    }

    public function getName()
    {
        return 'araneum_mail_form_mail_api';
    }
}