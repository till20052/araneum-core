<?php

namespace Araneum\Bundle\AgentBundle\Form\Filter;

use Araneum\Bundle\AgentBundle\Entity\Lead;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LeadFilterType
 *
 * @package Araneum\Bundle\MainBundle\Form\Filter
 */
class LeadFilterType extends AbstractType
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
                'application',
                'filter_entity',
                [
                    'label' => 'Application',
                    'class' => 'Araneum\Bundle\MainBundle\Entity\Application',
                    'multiple' => false,
                    'empty_value' => 'admin.general.SELECT',
                    'attr' => [
                        'translateLabel' => 'leads.Application',
                    ],
                    'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                        $query = $filterQuery->getQueryBuilder();
                        $query->innerJoin($field, 'application');
                        $value = $values['value'];
                        if (isset($value)) {
                            $query->andWhere($query->expr()->eq('application.id', $value->getId()));
                        }
                    },
                ]
            )
            ->add(
                'firstName',
                'filter_text',
                [
                    'label' => 'First Name',
                    'attr' => [
                        'placeholder' => 'leads.FIRST_NAME',
                        'translateLabel' => 'leads.FIRST_NAME',
                    ],
                ]
            )
            ->add(
                'lastName',
                'filter_text',
                [
                    'label' => 'Last Name',
                    'attr' => [
                        'placeholder' => 'leads.LAST_NAME',
                        'translateLabel' => 'leads.LAST_NAME',
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
