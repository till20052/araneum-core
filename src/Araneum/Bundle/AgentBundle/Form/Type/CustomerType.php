<?php

namespace Araneum\Bundle\AgentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CustomerType
 *
 * @package Araneum\Bundle\AgentBundle\Form
 */
class CustomerType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('country')
            ->add('email')
            ->add('phone')
            ->add(
                'birthday',
                'datetime',
                [
                    'date_format' => 'yyyy-MM-dd',
                    'widget' => 'single_text',
                ]
            )
            ->add('currency')
            ->add('siteId')
            ->add(
                'password',
                'text',
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(
                            [
                                'min' => 6,
                                'max' => 32,
                            ]
                        ),
                    ],
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
                'data_class' => 'Araneum\Bundle\AgentBundle\Entity\Customer',
                'csrf_protection' => false,
            ]
        );
    }
}
