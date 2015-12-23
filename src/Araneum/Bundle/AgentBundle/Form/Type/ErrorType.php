<?php

namespace Araneum\Bundle\AgentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ErrorType
 *
 * @package Araneum\Bundle\AgentBundle\Form
 */
class ErrorType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'error_type',
                null,
                [
                    'property_path' => 'type',
                ]
            )
            ->add(
                'error_message',
                null,
                [
                    'property_path' => 'message',
                ]
            )
            ->add(
                'error_sent_at',
                'datetime',
                [
                    'date_format' => 'yyyy-MM-ddTHH:mm:ss',
                    'widget' => 'single_text',
                    'property_path' => 'sentAt',
                ]
            );
    }

    /**
     * Set default options for form
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Araneum\Bundle\AgentBundle\Entity\Error',
                'csrf_protection' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'error';
    }
}
