<?php

namespace Araneum\Bundle\MainBundle\Form\Type;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

/**
 * Class ClusterType
 *
 * @package Araneum\Bundle\MainBundle\Form\Type
 */
class ClusterType extends AbstractType
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
                        'placeholder' => 'clusters.PLACEHOLDER',
                        'translateLabel' => 'clusters.NAME',
                    ],
                ]
            )
            ->add(
                'type',
                'choice',
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
                'choice',
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
                'choice',
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
     * @inheritdoc
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'action' => $this->router->generate('araneum_admin_main_cluster_post'),
                'data_class' => 'Araneum\Bundle\MainBundle\Entity\Cluster',
                'csrf_protection' => false,
            ]
        );
    }
}
