<?php

namespace Araneum\Bundle\UserBundle\Form\Filter;

use Araneum\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    /**
     * Doctrine
     *
     * @var
     */
    private $doctrine;

    /**
     * Container
     *
     * @var
     */
    private $container;

    /**
     * Constructor
     *
     * @param $doctrine
     * @param $container
     */
    public function __construct($doctrine, $container)
    {
        $this->doctrine = $doctrine;
        $this->container = $container;
    }

    /**
     * Build user form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            'filter_text',
            [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Enter email user'
                ],
            ]
        )
        ->add(
            'fullName',
            'filter_text',
            [
                'label' => 'Full name',
                'attr' => [
                    'placeholder' => 'Enter full name'
                ],
            ]
        )
        ->add(
            'enabled',
            'filter_choice',
            [
                'label' => 'Enabled',
                'choices' => User::$enable,
                'empty_value' => 'Choose line',
            ]
        )
        ->add(
            'lastLogin',
            'filter_text',
            [
                'label' => 'Last login',
                'attr'=>[
                    'placeholder' => 'Enter date'
                ]
            ]
        )
        ->add(
            'roles',
            'filter_choice',
            [
                'label' => 'Role',
                'choices' => User::$roleNames,
                'empty_value' => 'Choose line'
            ]
        );
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName()
    {
        return 'user_filter';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection'   => false,
                'validation_groups' => [
                    'filtering'
                ]
            ]
        );
    }
}