<?php

namespace Araneum\Bundle\MainBundle\Form\Type;

use Araneum\Bundle\MainBundle\Entity\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Araneum\Bundle\MainBundle\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class ApplicationAdminType
 *
 * @package Araneum\Bundle\MainBundle\Form\Type
 */
class ApplicationAdminType extends AbstractType
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
                HiddenType::class,
                [
                    'mapped' => false,
                ]
            )
            ->add(
                'cluster',
                'entity',
                [
                    'label' => 'Cluster',
                    'class' => 'Araneum\Bundle\MainBundle\Entity\Cluster',
                    'empty_value' => 'applications.EMPTY_VALUE',
                    'attr' => [
                        'placeholder' => 'applications.placeholder.CLUSTER',
                        'translateLabel' => 'applications.CLUSTER',
                    ],
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Name',
                    'attr' => [
                        'placeholder' => 'applications.PLACEHOLDER',
                        'translateLabel' => 'applications.NAME',
                    ],
                ]
            )
            ->add(
                'domain',
                TextType::class,
                [
                    'label' => 'Domain',
                    'attr' => [
                        'placeholder' => 'applications.ENTER_DOMAIN',
                        'translateLabel' => 'applications.DOMAIN',
                    ],
                ]
            )
            ->add(
                'aliases',
                TextType::class,
                [
                    'label' => 'Aliases',
                    'attr' => [
                        'placeholder' => 'applications.ENTER_ALIASES',
                        'translateLabel' => 'applications.ALIASES',
                    ],
                ]
            )
            ->add(
                'useSsl',
                'checkbox',
                [
                    'label' => 'UseSsl',
                    'attr' => [
                        'translateLabel' => 'applications.USE_SSL',
                    ],
                ]
            )
            ->add(
                'enabled',
                'checkbox',
                [
                    'label' => 'Enabled',
                    'attr' => [
                        'translateLabel' => 'applications.ENABLED',
                    ],
                ]
            )
            ->add(
                'type',
                'choice',
                [
                    'label' => 'Type',
                    'empty_value' => 'applications.EMPTY_VALUE',
                    'choices' => Application::$types,
                    'attr' => [
                        'placeholder' => 'applications.ENTER_TYPE',
                        'translateLabel' => 'applications.TYPE',
                    ],
                ]
            )
            ->add(
                'locales',
                'entity',
                [
                    'class' => 'Araneum\Bundle\MainBundle\Entity\Locale',
                    'property' => 'name',
                    'multiple' => true,
                    'attr' => [
                        'placeholder' => 'applications.placeholder.LOCALE',
                        'translateLabel' => 'applications.LOCALE',
                    ],
                ]
            )
            ->add(
                'template',
                TextType::class,
                [
                    'label' => 'Template',
                    'attr' => [
                        'placeholder' => 'applications.ENTER_TEMPLATE',
                        'translateLabel' => 'applications.TEMPLATE',
                    ],
                ]
            )
            ->add(
                'spotApiUser',
                TextType::class,
                [
                    'label' => 'spotApiUser',
                    'attr' => [
                        'placeholder' => 'applications.ENTER_SPOT_API_USER',
                        'translateLabel' => 'applications.SPOT_API_USER',
                    ],
                ]
            )
            ->add(
                'spotApiPassword',
                TextType::class,
                [
                    'label' => 'spotApiPassword',
                    'attr' => [
                        'placeholder' => 'applications.ENTER_SPOT_API_PASSWORD',
                        'translateLabel' => 'applications.SPOT_API_PASSWORD',
                    ],
                ]
            )
            ->add(
                'spotApiUrl',
                UrlType::class,
                [
                    'label' => 'spotApiUrl',
                    'attr' => [
                        'placeholder' => 'applications.ENTER_SPOT_API_URL',
                        'translateLabel' => 'applications.SPOT_API_URL',
                    ],
                ]
            )
            ->add(
                'spotApiPublicUrl',
                UrlType::class,
                [
                    'label' => 'spotApiPublicUrl',
                    'attr' => [
                        'placeholder' => 'applications.ENTER_SPOT_API_PUBLIC_URL',
                        'translateLabel' => 'applications.SPOT_API_PUBLIC_URL',
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
                'action' => $this->router->generate('araneum_admin_main_application_post'),
                'data_class' => 'Araneum\Bundle\MainBundle\Entity\Application',
                'csrf_protection' => false,
            ]
        );
    }
}
