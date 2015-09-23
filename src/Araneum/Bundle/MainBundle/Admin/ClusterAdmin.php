<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 18.09.15
 * Time: 17:29
 */

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class ClusterAdmin extends Admin
{
    /**
     * Fields to be shown on create/edit forms
     *
     * @params FormMapper
     * */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('name', 'text', ['label' => 'name'])
                ->add('host', 'sonata_type_model', [], [])
                ->add('type', 'choice', [
                        'choices' => [
                            Cluster::TYPE_MULTIPLE => 'multiple',
                            Cluster::TYPE_SINGLE => 'single'
                        ],
                        'label' => 'Type'
                    ])
                ->add('status', 'choice', [
                        'choices' => [
                            Cluster::STATUS_ONLINE => 'online',
                            Cluster::STATUS_OFFLINE => 'offline'
                        ],
                        'label' => 'Status'
                    ])
                ->add('enabled', 'checkbox', ['label' => 'enabled', 'required' => false]);
    }

    /**
     * Fields to be shown on the filter panel
     *
     * @params DataGridMapper
     * */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'name'])
            ->add('host', null, ['label' => 'host'])
            ->add('type', null, ['label' => 'type'])
            ->add('status', null, ['label' => 'status'])
            ->add('enabled', null, ['label' => 'enabled'])
            ->add('createdAt', 'doctrine_orm_datetime_range', [
                'label' => 'created_at',
                'field_type' => 'sonata_type_datetime_range_picker',
                'pattern' => 'MM.dd.YYY'
            ]);
    }

    /**
     * Fields to be shown on list
     *
     * @params ListMapper
     * */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name', 'text', ['editable' => true])
            ->add('host', 'text', ['editable' => true])
            ->add('type', 'choice', [
                    'choices' => [
                            Cluster::TYPE_MULTIPLE => 'multiple',
                            Cluster::TYPE_SINGLE => 'single'
                        ],
                    'label' => 'Type'
                ])
            ->add('status', 'choice', [
                    'choices' => [
                            Cluster::STATUS_ONLINE => 'online',
                            Cluster::STATUS_OFFLINE => 'offline'
                        ],
                    'label' => 'Status'
                ])
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt', 'datetime', ['format' => 'm.d.Y'])
            ->add('_action', 'actions', [
                    'actions' => [
                        'edit' => [],
                        'check_status' => [
                            'template' => 'AraneumMainBundle:Admin:checkStatus.html.twig'
                        ],
                        'delete' => []
                    ]
                ]);
    }


    /**
     * Configure routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('checkStatus', 'checkStatus/{id}', ['_controller'=>'AraneumMainBundle:CRUD:checkStatus']);
    }
}