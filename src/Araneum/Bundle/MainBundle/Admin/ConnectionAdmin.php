<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

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
                    'label' => 'Type',
                    'choices' => [
                        Connection::CONN_DB => 'DB connection',
                        Connection::CONN_HOST => 'Host Connection'
                    ]
                ]
            )
            ->add('name', 'text', ['label' => 'Name'])
            ->add('host', 'text', ['label' => 'Host'])
            ->add('port', 'integer', ['label' => 'Port'])
            ->add('userName', 'text', ['label' => 'User Name'])
            ->add('enabled', 'checkbox',
                [
                    'label' => 'Enabled',
                    'required' => false
                ]
            )
            ->add('password', 'text', ['label' => 'Password']);
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
                    'label' => 'Type',
                    'choices' => [
                        Connection::CONN_DB => 'DB connection',
                        Connection::CONN_HOST => 'Host Connection'
                    ]
                ]
            )
            ->add('name', 'text', ['label' => 'Name'])
            ->add('host', 'text', ['label' => 'Host'])
            ->add('port', 'integer', ['label' => 'Post'])
            ->add('userName', 'text', ['label' => 'User Name'])
            ->add('enabled', 'checkbox', ['label' => 'Enabled'])
            ->add('password', 'password', ['label' => 'Password']);
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
                    'label' => 'Type',
                    'choices' => [
                        Connection::CONN_DB => 'DB connection',
                        Connection::CONN_HOST => 'Host Connection'
                    ]
                ]
            )
            ->add('name', null, ['label' => 'Name'])
            ->add('host', null, ['label' => 'Host'])
            ->add('port', null, ['label' => 'Port'])
            ->add('userName', null, ['label' => 'User Name'])
            ->add('enabled', null, ['label' => 'Enabled'])
            ->add('createdAt', 'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'label' => 'Created At'
                ]
            )
            ->add('updatedAt', 'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'label' => 'Updated At'
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
                    'label' => 'Type',
                    'choices' => [
                        Connection::CONN_DB => 'DB connection',
                        Connection::CONN_HOST => 'Host Connection'
                    ]
                ]
            )
            ->add('name', 'text', ['editable' => true])
            ->add('host', 'text', ['editable' => true])
            ->add('port', 'integer', ['editable' => true])
            ->add('userName', 'text', ['editable' => true])
            ->add(
                'enabled',
                'boolean',
                [
                    'editable' => true
                ]
            )
            ->add('createdAt', 'datetime', ['format' => 'm/d/Y'])
            ->add('updatedAt', 'datetime', ['format' => 'm/d/Y'])
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => [],
                        'testConnection' => [
                            'template' => 'AraneumMainBundle:Admin:testConnection.html.twig'
                        ],
                        'delete' => [],
                    ],
                ]
            );
    }

    /**
     * Configure routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('testConnection', 'testConnection/{id}', ['_controller'=>'AraneumMainBundle:CRUD:testConnection']);
    }

}