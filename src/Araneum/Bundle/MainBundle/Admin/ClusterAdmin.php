<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 18.09.15
 * Time: 17:29
 */

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Repository\ConnectionRepository;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class ClusterAdmin extends Admin
{
    /**
     * @var ConnectionRepository
     */
    private $connectionRepository;

    /**
     * @param EntityRepository $connectionRepository
     */
    public function setConnectionRepository(EntityRepository $connectionRepository)
    {
        $this->connectionRepository = $connectionRepository;
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @params FormMapper
     * */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['label' => 'name'])
            ->add(
                'hosts',
                'sonata_type_model',
                [
                    'multiple' => true,
                    'by_reference' => false,
                    'required' => false
                ]
            )
            ->add(
                'type',
                'choice',
                [
                    'choices' => [
                        Cluster::TYPE_MULTIPLE => 'multiple',
                        Cluster::TYPE_SINGLE => 'single'
                    ],
                    'label' => 'type'
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'choices' => [
                        Cluster::STATUS_ONLINE => 'online',
                        Cluster::STATUS_OFFLINE => 'offline'
                    ],
                    'label' => 'status'
                ]
            )
            ->add(
                'enabled',
                'checkbox',
                [
                    'label' => 'enabled',
                    'required' => false
                ]
            );
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
            ->add('hosts', null, ['label' => 'host'])
            ->add(
                'type',
                null,
                [
                    'label' => 'type'
                ],
                'choice',
                [
                    'choices' => [
                        Cluster::TYPE_MULTIPLE => 'multiple',
                        Cluster::TYPE_SINGLE => 'single'
                    ]
                ]
            )
            ->add(
                'status',
                null,
                [
                    'label' => 'status'
                ],
                'choice',
                [
                    'choices' => [
                        Cluster::STATUS_ONLINE => 'online',
                        Cluster::STATUS_OFFLINE => 'offline'
                    ]
                ]
            )
            ->add('enabled', null, ['label' => 'enabled'])
            ->add(
                'created_at',
                'doctrine_orm_datetime_range',
                [
                    'label' => 'created_at',
                    'field_type' => 'sonata_type_datetime_range_picker',
                    'pattern' => 'MM.dd.YYY'
                ]
            );
    }

    /**
     * Fields to be shown on list
     *
     * @params ListMapper
     * */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add(
                'name',
                'text',
                [
                    'editable' => true,
                    'label' => 'name'
                ]
            )
            ->add(
                'hosts',
                'sonata_type_model',
                [
                    'editable' => false,
                    'label' => 'host'
                ]
            )
            ->add(
                'type',
                'choice',
                [
                    'choices' => [
                        Cluster::TYPE_MULTIPLE => 'multiple',
                        Cluster::TYPE_SINGLE => 'single'
                    ],
                    'label' => 'type'
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'choices' => [
                        Cluster::STATUS_ONLINE => 'online',
                        Cluster::STATUS_OFFLINE => 'offline'
                    ],
                    'label' => 'status'
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'editable' => true,
                    'label' => 'enabled',
                    'row_align' => 'center',
                    'text_align' => 'center'
                ]
            )
            ->add(
                'created_at',
                'datetime',
                [
                    'format' => 'm.d.Y',
                    'label' => 'created_at'
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'label' => 'actions',
                    'actions' => [
                        'edit' => [],
                        'check_status' => [
                            'template' => 'AraneumMainBundle:AdminAction:checkStatusCluster.html.twig'
                        ],
                        'delete' => []
                    ]
                ]
            );
    }
}