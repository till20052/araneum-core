<?php

namespace Araneum\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProfileType
 *
 * @package Araneum\Bundle\UserBundle\Form\Type
 */
class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                [
                    'label' => 'login',
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'fullName',
                'text',
                [
                    'required' => false,
                    'label' => 'full_name',
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'email',
                'text',
                [
                    'label' => 'email',
                    'attr' => ['class' => 'form-control'],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => ['Profile'],
            ]
        );
    }
}
