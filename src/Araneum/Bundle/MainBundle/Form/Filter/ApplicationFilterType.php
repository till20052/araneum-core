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
                    'label' => 'Cluster app',
                    'class' => 'Araneum\Bundle\MainBundle\Entity\Cluster',
                    'multiple' => false,
                    'empty_value' => 'admin.general.SELECT',
                    'attr' => [
                        'translateLabel' => 'Cluster app',
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
                    'label' => 'Name app',
                    'attr' => [
                        'placeholder' => 'name placeholder',
                        'translateLabel' => 'Name app',
                    ],
                ]
            )
            ->add(
                'domain',
                'filter_text',
                [
                    'label' => 'Domain app',
                    'attr' => [
                        'placeholder' => 'Domain placeholder',
                        'translateLabel' => 'Domain app',
                    ],
                ]
            )
            ->add(
                'type',
                'filter_text',
                [
                    'label' => 'Type app',
                    'attr' => [
                        'placeholder' => 'Type placeholder',
                        'translateLabel' => 'Type app',
                    ],
                ]
            )
            ->add(
                'status',
                'filter_choice',
                [
                    'label' => 'Status app',
                    'choices' => Application::getStatuses(),
                    'empty_value' => 'locales.EMPTY_VALUE',
                    'attr' => [
                        'translateLabel' => 'Status app',
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
