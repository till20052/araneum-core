<?php

namespace Araneum\Bundle\UserBundle\Form\Type;

use Araneum\Bundle\MainBundle\Entity\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

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
                'name',
                'text',
                [
                    'label' => 'Name',
                    'attr' => [
                        'placeholder' => 'locales.PLACEHOLDER',
                        'translateLabel' => 'locales.NAME',
                    ],
                ]
            )
            ->add(
                'locale',
                'text',
                [
                    'label' => 'Locale',
                    'attr' => [
                        'placeholder' => 'locales.ENTER_LOCALE',
                        'translateLabel' => 'locales.LOCALE',
                    ],
                ]
            )
            ->add(
                'enabled',
                'choice',
                [
                    'label' => 'Enabled',
                    'choices' => Locale::$enable,
                    'empty_value' => 'locales.EMPTY_VALUE',
                    'attr' => [
                        'translateLabel' => 'locales.ENABLED',
                    ],
                ]
            )
            ->add(
                'orientation',
                'choice',
                [
                    'label' => 'Orientation',
                    'choices' => Locale::$orientations,
                    'empty_value' => 'locales.EMPTY_VALUE',
                    'attr' => [
                        'translateLabel' => 'locales.ORIENTATION',
                    ],
                ]
            )
            ->add(
                'encoding',
                'text',
                [
                    'label' => 'Encoding',
                    'attr' => [
                        'placeholder' => 'locales.ENTER_ENCODING',
                        'translateLabel' => 'locales.ENCODING',
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
