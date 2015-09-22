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
            ->add('name', 'text', ['label' => 'Name'])
            ->add('host', 'sonata_type_model', [], [
                'target_entity' => 'Araneum\Bundle\MainBundle\Entity\Connection'
            ])
            ->add('type', 'choice', [
                    'choices' => [
                        Cluster::TYPE_MULTIPLE => 'Multiple',
                        Cluster::TYPE_SINGLE => 'Single'
                    ],
                    'label' => 'Type'
                ])
            ->add('status', 'choice', [
                    'choices' => [
                            Cluster::STATUS_ONLINE => 'Online',
                            Cluster::STATUS_OFFLINE => 'Offline'
                        ],
                    'label' => 'Status'
                ])
            ->add('enabled', 'checkbox', ['label' => 'Enabled', 'required' => false])
            ->add('createdAt', 'sonata_type_date_picker', ['label' => 'Created at']);
    }

    /**
     * Fields to be shown on the filter panel
     *
     * @params DataGridMapper
     * */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Name'])
            ->add('host', null, ['label' => 'Host'])
            ->add('type', null, ['label' => 'Type'])
            ->add('status', null, ['label' => 'Status'])
            ->add('enabled', null, ['label' => 'Enabled'])
            ->add('createdAt', 'doctrine_orm_datetime_range', [
                'label' => 'Created at',
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
                            Cluster::TYPE_MULTIPLE => 'Multiple',
                            Cluster::TYPE_SINGLE => 'Single'
                        ],
                    'label' => 'Type'
                ])
            ->add('status', 'choice', [
                    'choices' => [
                            Cluster::STATUS_ONLINE => 'Online',
                            Cluster::STATUS_OFFLINE => 'Offline'
                        ],
                    'label' => 'Status'
                ])
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt', 'datetime', ['format' => 'm.d.Y'])
            ->add('_action', 'actions', [
                    'actions' => [
                        'show' => [],
                        'edit' => [],
                        'delete' => []
                    ]
                ]);
    }
}