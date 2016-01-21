<?php

namespace Araneum\Bundle\UserBundle\Form\Type;

use Araneum\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Araneum\Bundle\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

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
//            ->add(
//                'role',
//                'choice',
//                [
//                    'label' => 'Role',
//                    'choices' => User::$roleNames
//                ]
//            )
            ->add(
                'password',
                'password',
                [
                    'label' => 'Full name',
                    'attr' => [
                        'placeholder' => 'user.DATA_GRID.PASSWORD',
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
