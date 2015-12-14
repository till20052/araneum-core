<?php

namespace Araneum\Bundle\UserBundle\Form\Filter;

use Araneum\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserFilterType
 *
 * @package Araneum\Bundle\UserBundle\Form\Filter
 */
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
     * @param EntityManager      $doctrine
     * @param ContainerInterface $container
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
                    'placeholder' => 'user.ENTER_EMAIL',
                    'translateLabel' => 'Email',
                ],
            ]
        )
            ->add(
                'fullName',
                'filter_text',
                [
                    'label' => 'Full name',
                    'attr' => [
                        'placeholder' => 'user.ENTER_FULLNAME',
                        'translateLabel' => 'user.FULLNAME',

                    ],
                ]
            )
            ->add(
                'enabled',
                'filter_choice',
                [
                    'label' => 'Enabled',
                    'choices' => User::$enable,
                    'empty_value' => 'admin.general.SELECT',
                    'attr' => [
                        'translateLabel' => 'admin.general.ENABLED',
                    ],
                ]
            )
            ->add(
                'lastLogin',
                'filter_text',
                [
                    'label' => 'Last login',
                    'attr' => [
                        'placeholder' => 'Enter date',
                        'translateLabel' => 'user.LAST_LOGIN',
                    ],
                ]
            )
            ->add(
                'roles',
                'filter_choice',
                [
                    'label' => 'Role',
                    'choices' => User::$roleNames,
                    'empty_value' => 'admin.general.SELECT',
                    'attr' => [
                        'translateLabel' => 'user.ROLE',
                    ],
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
                'csrf_protection' => false,
                'validation_groups' => [
                    'filtering',
                ],
            ]
        );
    }
}
