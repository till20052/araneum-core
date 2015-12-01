<?php

namespace Araneum\Bundle\MainBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleFilterType extends AbstractType
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
            'name',
            'filter_text',
            [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Enter locale name'
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
                'csrf_protection'   => false,
                'validation_groups' => [
                    'filtering'
                ]
            ]
        );
    }
}