<?php

namespace Araneum\Bundle\MainBundle\Form\Filter;

use Araneum\Bundle\MainBundle\Entity\Locale;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LocaleFilterType
 *
 * @package Araneum\Bundle\MainBundle\Form\Filter
 */
class LocaleFilterType extends AbstractType
{
    private $doctrine;
    private $container;

    /**
     * Constructor
     *
     * @param Registry           $doctrine
     * @param ContainerInterface $container
     */
    public function __construct(Registry $doctrine, ContainerInterface $container)
    {
        $this->doctrine = $doctrine;
        $this->container = $container;
    }

    /**
     * Build user form
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'filter_text',
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
                'filter_text',
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
                'filter_choice',
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
                'filter_choice',
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
                'filter_text',
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
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection'   => false,
                'validation_groups' => [
                    'filtering',
                ],
            ]
        );
    }
}
