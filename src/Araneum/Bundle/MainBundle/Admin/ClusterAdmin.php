<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\ApplicationEvents;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Repository\ConnectionRepository;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;

/**
 * Class ClusterAdmin
 * @package Araneum\Bundle\MainBundle\Admin
 */
class ClusterAdmin extends Admin
{
    /**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;

	/**
	 * Invoke method after creation of cluster
	 *
	 * @param Cluster $cluster
	 * @return void
	 */
	public function postPersist($cluster)
	{
		$event = new ApplicationEvent();

		$event->setApplications($cluster->getApplications());

		$this->dispatcher->dispatch(ApplicationEvents::POST_PERSIST, $event);
	}

	/**
	 * Invoke method after modification of cluster
	 *
	 * @param Cluster $cluster
	 * @return void
	 */
	public function postUpdate($cluster)
	{
		$event = new ApplicationEvent();

		$event->setApplications($cluster->getApplications());

		$this->dispatcher->dispatch(ApplicationEvents::POST_UPDATE, $event);
	}

	/**
	 * Invoke method after deletion of cluster
	 *
	 * @param Cluster $cluster
	 * @return void
	 */
	public function preRemove($cluster)
	{
		$event = new ApplicationEvent();

		$event->setApplications($cluster->getApplications());

        $this->dispatcher->dispatch(ApplicationEvents::PRE_REMOVE, $event);
	}

	/**
	 * Set Event Dispatcher
	 *
	 * @param EventDispatcherInterface $eventDispatcherInterface
	 */
	public function setDispatcher(EventDispatcherInterface $eventDispatcherInterface)
	{
		$this->dispatcher = $eventDispatcherInterface;
	}
    
    /**
     * Get Batch
     *
     * @return array
     */
    public function getBatchActions()
    {
        return array_merge(
            parent::getBatchActions(),
            [
                'checkStatus' => [
                    'label' => 'Check Status',
                    'ask_confirmation' => true
                ]
            ]
        );
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
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
                    'choices' => Cluster::getStatuses(),
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
     * @param DataGridMapper $datagridMapper
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
                    'choices' => Cluster::getStatuses()
                ]
            )
            ->add('enabled', null, ['label' => 'enabled'])
            ->add(
                'createdAt',
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
     * @param ListMapper $listMapper
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
                    'choices' => Cluster::getStatuses(),
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
                'createdAt',
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