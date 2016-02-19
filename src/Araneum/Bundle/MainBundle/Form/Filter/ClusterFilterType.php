<?php

namespace Araneum\Bundle\MainBundle\Form\Filter;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ClusterFilterType
 *
 * @package Araneum\Bundle\MainBundle\Form\Filter
 */
class ClusterFilterType extends AbstractType
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
        $builder
            ->add(
                'name',
                'filter_text',
                [
                    'label' => 'Name',
                    'attr' => [
                        'placeholder' => 'clusters.PLACEHOLDER',
                        'translateLabel' => 'clusters.NAME',
                    ],
                ]
            )
            ->add(
                'type',
                'filter_choice',
                [
                    'label' => 'Type',
                    'choices' => Cluster::getTypes(),
                    'empty_value' => 'clusters.EMPTY_VALUE',
                    'attr' => [
                        'translateLabel' => 'clusters.TYPE',
                    ],
                ]
            )
            ->add(
                'enabled',
                'filter_choice',
                [
                    'label' => 'Enabled',
                    'choices' => Cluster::$enable,
                    'empty_value' => 'clusters.EMPTY_VALUE',
                    'attr' => [
                        'translateLabel' => 'clusters.ENABLED',
                    ],
                ]
            )
            ->add(
                'status',
                'filter_choice',
                [
                'label' => 'Status',
                'choices' => Cluster::getStatuses(),
                'empty_value' => 'clusters.EMPTY_VALUE',
                'attr' => [
                    'translateLabel' => 'clusters.STATUS',
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
