<?php

namespace Araneum\Bundle\MainBundle\Form\Filter;

use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

/**
 * Class ApplicationFilterType
 *
 * @package Araneum\Bundle\MainBundle\Form\Filter
 */
class ApplicationFilterType extends AbstractType
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
     * Build application form
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'cluster',
                'filter_entity',
                [
                    'label' => 'Cluster',
                    'class' => 'Araneum\Bundle\MainBundle\Entity\Cluster',
                    'multiple' => false,
                    'empty_value' => 'admin.general.SELECT',
                    'attr' => [
                        'translateLabel' => 'applications.CLUSTER',
                    ],
                    'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                        $query = $filterQuery->getQueryBuilder();
                        $query->innerJoin($field, 'cluster');
                        $value = $values['value'];
                        if (isset($value)) {
                            $query->andWhere($query->expr()->eq('cluster.id', $value->getId()));
                        }
                    },
                ]
            )
            ->add(
                'name',
                'filter_text',
                [
                    'label' => 'Name',
                    'attr' => [
                        'placeholder' => 'applications.placeholder.NAME',
                        'translateLabel' => 'applications.NAME',
                    ],
                ]
            )
            ->add(
                'domain',
                'filter_text',
                [
                    'label' => 'Domain',
                    'attr' => [
                        'placeholder' => 'applications.placeholder.DOMAIN',
                        'translateLabel' => 'applications.DOMAIN',
                    ],
                ]
            )
            ->add(
                'type',
                'filter_text',
                [
                    'label' => 'Type',
                    'attr' => [
                        'placeholder' => 'applications.placeholder.TYPE',
                        'translateLabel' => 'applications.TYPE',
                    ],
                ]
            )
            ->add(
                'status',
                'filter_choice',
                [
                    'label' => 'Status',
                    'choices' => Application::getStatuses(),
                    'empty_value' => 'admin.general.SELECT',
                    'attr' => [
                        'translateLabel' => 'applications.STATUS',
                    ],
                ]
            )
        ;
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
