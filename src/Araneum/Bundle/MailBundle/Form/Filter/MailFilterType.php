<?php

namespace Araneum\Bundle\MailBundle\Form\Filter;

use Araneum\Bundle\MailBundle\Entity\Mail;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

/**
 * Class MailFilterType
 *
 * @package Araneum\Bundle\MailBundle\Form\Filter
 */
class MailFilterType extends AbstractType
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
     * Build mail form
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
                        'translateLabel' => 'mails.APPLICATION',
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
                'sender',
                'filter_text',
                [
                    'label' => 'Sender',
                    'attr' => [
                        'placeholder' => 'mails.placeholder.SENDER',
                        'translateLabel' => 'mails.SENDER',
                    ],
                ]
            )
            ->add(
                'target',
                'filter_text',
                [
                    'label' => 'Target',
                    'attr' => [
                        'placeholder' => 'mails.placeholder.TARGET',
                        'translateLabel' => 'mails.TARGET',
                    ],
                ]
            )
            ->add(
                'headline',
                'filter_text',
                [
                    'label' => 'Headline',
                    'attr' => [
                        'placeholder' => 'mails.placeholder.HEADLINE',
                        'translateLabel' => 'mails.HEADLINE',
                    ],
                ]
            )
            ->add(
                'status',
                'filter_choice',
                [
                    'label' => 'Status',
                    'choices' => Mail::$statuses,
                    'empty_value' => 'admin.general.SELECT',
                    'attr' => [
                        'translateLabel' => 'mails.STATUS',
                    ],
                ]
            )
            ->add(
                'sentAt',
                'filter_text',
                [
                    'label' => 'Sent at',
                    'attr' => [
                        'translateLabel' => 'mails.SENT_AT',
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
