<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class ConnectionAdmin
 *
 * @package Araneum\Bundle\MainBundle\Admin
 */
class ConnectionAdmin extends Admin
{
    /**
     * Create/Update form
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'type',
                'choice',
                [
                    'label' => 'type',
                    'choices' => [
                        Connection::CONN_DB => 'db_connection',
                        Connection::CONN_HOST => 'host_connection'
                    ]
                ]
            )
            ->add('name', 'text', ['label' => 'name'])
            ->add('host', 'text', ['label' => 'host'])
            ->add('port', 'integer', ['label' => 'port'])
            ->add('userName', 'text', ['label' => 'username'])
            ->add(
                'enabled',
                'checkbox',
                [
                    'label' => 'enabled',
                    'required' => false
                ]
            )
            ->add('password', 'text', ['label' => 'password']);
    }

    /**
     * Show fields
     *
     * @param ShowMapper $formMapper
     */
    protected function configureShowFields(ShowMapper $formMapper)
    {
        $formMapper
            ->add(
                'type',
                'choice',
                [
                    'label' => 'type',
                    'choices' => [
                        Connection::CONN_DB => 'db_connection',
                        Connection::CONN_HOST => 'host_connection'
                    ]
                ]
            )
            ->add('name', 'text', ['label' => 'name'])
            ->add('host', 'text', ['label' => 'host'])
            ->add('port', 'integer', ['label' => 'port'])
            ->add('userName', 'text', ['label' => 'username'])
            ->add('enabled', 'checkbox', ['label' => 'enabled'])
            ->add('password', 'password', ['label' => 'password']);
    }

    /**
     * Filters for list
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'type',
                'doctrine_orm_choice',
                [],
                'choice',
                [
                    'label' => 'type',
                    'choices' => [
                        Connection::CONN_DB => 'db_connection',
                        Connection::CONN_HOST => 'host_connection'
                    ]
                ]
            )
            ->add('name', null, ['label' => 'name'])
            ->add('host', null, ['label' => 'host'])
            ->add('port', null, ['label' => 'port'])
            ->add('userName', null, ['label' => 'username'])
            ->add('enabled', null, ['label' => 'enabled'])
            ->add(
                'createdAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'label' => 'created_at'
                ],
                null,
                [
                    'format' => 'MM/dd/yyyy'
                ]
            )
            ->add(
                'updatedAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'label' => 'updated_at'
                ],
                null,
                [
                    'format' => 'MM/dd/yyyy'
                ]
            );
    }

    /**
     * Show list
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'type',
                'choice',
                [
                    'label' => 'type',
                    'choices' => [
                        Connection::CONN_DB => $this->getTranslator()->trans('db_connection'),
                        Connection::CONN_HOST => $this->getTranslator()->trans('db_connection')
                    ]
                ]
            )
            ->add('name', 'text',
                [
                    'label' => 'name',
                    'editable' => true
                ]
            )
            ->add('host', 'text',
                [
                    'label' => 'host',
                    'editable' => true
                ]
            )
            ->add('port', 'integer',
                [
                    'label' => 'port',
                    'editable' => true
                ]
            )
            ->add('userName', 'text',
                [
                    'label' => 'username',
                    'editable' => true
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                    'editable' => true
                ]
            )
            ->add('createdAt', 'datetime',
                [
                    'label' => 'created_at',
                    'format' => 'm/d/Y'
                ]
            )
            ->add('updatedAt', 'datetime',
                [
                    'label' => 'updated_at',
                    'format' => 'm/d/Y'
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'label' => 'actions',
                    'actions' => [
                        'edit' => [],
                        'testConnection' => [
                            'template' => 'AraneumMainBundle:AdminAction:testConnection.html.twig'
                        ],
                        'delete' => [],
                    ],
                ]
            );
    }
}