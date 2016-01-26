<?php

namespace Araneum\Bundle\UserBundle\Form\Type;

use Araneum\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;
use Araneum\Bundle\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserType
 *
 * @package Araneum\Bundle\UserBundle\Form\Type
 */
class UserType extends AbstractType
{
    protected $router;

    /**
     * UserAdminType constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritdoc
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                'hidden',
                [
                    'mapped' => false,
                ]
            )
            ->add(
                'username',
                'text',
                [
                    'label' => 'Full name',
                    'attr' => [
                        'placeholder' => 'user.data_grid.PLACEHOLDER',
                        'translateLabel' => 'user.data_grid.NAME',
                    ],
                ]
            )
            ->add(
                'fullName',
                'text',
                [
                    'label' => 'Full name',
                    'attr' => [
                        'placeholder' => 'user.data_grid.PLACEHOLDER',
                        'translateLabel' => 'user.data_grid.FULL_NAME',
                    ],
                ]
            )
            ->add(
                'email',
                'email',
                [
                    'label' => 'Email',
                    'attr' => [
                        'placeholder' => 'user.data_grid.EMAIL',
                        'translateLabel' => 'user.data_grid.EMAIL',
                    ],
                ]
            )
            ->add(
                'enabled',
                'choice',
                [
                    'label' => 'Enabled',
                    'choices' => User::$enable,
                    'empty_value' => 'user.data_grid.EMPTY_VALUE',
                    'attr' => [
                        'translateLabel' => 'user.data_grid.ENABLED',
                    ],
                ]
            )
            ->add(
                'role',
                'choice',
                [
                    'choices' => User::$roleNames,
                    'choices_as_values' => true,
                    'label'         => 'Roles',
                    'empty_value'   => 'user.data_grid.SELECT_ROLES',
                    'attr' => [
                        'translateLabel' => 'user.data_grid.ROLES',
                    ],
                ]
            )
            ->add(
                'plainPassword',
                'password',
                [
                    'label'         => 'Password',
                    'attr' => [
                        'placeholder' => 'user.data_grid.PASSWORD_PLACEHOLDER',
                        'translateLabel' => 'user.data_grid.PASSWORD',
                    ],
                ]
            );
    }

    /**
     * @inheritdoc
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'action' => $this->router->generate('araneum_user_admin_user_post'),
                'data_class' => 'Araneum\Bundle\UserBundle\Entity\User',
                'csrf_protection' => false,
            ]
        );
    }
}
