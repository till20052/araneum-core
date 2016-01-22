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
                        'placeholder' => 'user.DATA_GRID.PLACEHOLDER',
                        'translateLabel' => 'user.DATA_GRID.FULL_NAME',
                    ],
                ]
            )
            ->add(
                'fullName',
                'text',
                [
                    'label' => 'Full name',
                    'attr' => [
                        'placeholder' => 'user.DATA_GRID.PLACEHOLDER',
                        'translateLabel' => 'user.DATA_GRID.FULL_NAME',
                    ],
                ]
            )
            ->add(
                'email',
                'email',
                [
                    'label' => 'Email',
                    'attr' => [
                        'placeholder' => 'user.DATA_GRID.EMAIL',
                        'translateLabel' => 'user.DATA_GRID.EMAIL',
                    ],
                ]
            )
            ->add(
                'enabled',
                'choice',
                [
                    'label' => 'Enabled',
                    'choices' => User::$enable,
                    'empty_value' => 'user.DATA_GRID.EMPTY_VALUE',
                    'attr' => [
                        'translateLabel' => 'user.DATA_GRID.ENABLED',
                    ],
                ]
            )
            ->add(
                'roles',
                'entity',
                [
                    'property_path' => 'role',
                    'class'         => 'AraneumUserBundle:Role',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                            ->orderBy('r.id');
                    },
                    'label'         => 'Roles',
                    'empty_value'   => 'user.DATA_GRID.SELECT_ROLES',
                    'attr' => [
                        'translateLabel' => 'user.DATA_GRID.ROLES',
                    ],
                ]
            )
            ->add(
                'plainPassword',
                'password',
                [
                    'label'         => 'Password',
                    'attr' => [
                        'placeholder' => 'user.DATA_GRID.PASSWORD_PLACEHOLDER',
                        'translateLabel' => 'user.DATA_GRID.PASSWORD',
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
