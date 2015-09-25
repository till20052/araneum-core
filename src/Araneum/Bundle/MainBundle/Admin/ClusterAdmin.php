<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 18.09.15
 * Time: 17:29
 */

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Repository\ConnectionRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class ClusterAdmin extends Admin
{
    /** @var ConnectionRepository */
    private $connectionRepository;

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
                'host',
                'sonata_type_model',
                [
                    'label' => 'host',
                    'query' => $this->connectionRepository->getQueryByUnusedAndType(Connection::CONN_HOST),
                ]
            )
            ->add('type', 'choice', [
                'choices' => [
                    Cluster::TYPE_MULTIPLE => 'multiple',
                    Cluster::TYPE_SINGLE => 'single'
                ],
                'label' => 'type'
            ])
            ->add('status', 'choice', [
                'choices' => [
                    Cluster::STATUS_ONLINE => 'online',
                    Cluster::STATUS_OFFLINE => 'offline'
                ],
                'label' => 'status'
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
            ->add('name', 'text', ['editable' => true, 'label' => 'name'])
            ->add('host', 'text', ['editable' => false, 'label' => 'host'])
            ->add('type', 'choice', [
                'choices' => [
                    Cluster::TYPE_MULTIPLE => 'multiple',
                    Cluster::TYPE_SINGLE => 'single'
                ],
                'label' => 'type'
            ])
            ->add('status', 'choice', [
                'choices' => [
                    Cluster::STATUS_ONLINE => 'online',
                    Cluster::STATUS_OFFLINE => 'offline'
                ],
                'label' => 'status'
            ])
            ->add('enabled', null, [
                'editable' => true,
                'label' => 'enabled',
                'row_align' => 'center',
                'text_align' => 'center'
            ])
            ->add('createdAt', 'datetime', ['format' => 'm.d.Y', 'label' => 'created_at'])
            ->add('_action', 'actions', ['label' => 'action',
                'actions' => [
                    'edit' => [],
                    'check_status' => [
                        'template' => 'AraneumMainBundle:AdminAction:checkStatus.html.twig'
                    ],
                    'delete' => []
                ]
            ]);
    }
}