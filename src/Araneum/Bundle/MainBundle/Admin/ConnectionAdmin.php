<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\ApplicationEvents;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ConnectionAdmin
 * @package Araneum\Bundle\MainBundle\Admin
 */
class ConnectionAdmin extends Admin
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;

	/**
	 * Get Array of Applications by Cluster Id
	 *
	 * @param $id
	 * @return ArrayCollection
	 */
	public function getApplications($id)
	{
		return $this->entityManager
			->getRepository('AraneumMainBundle:Connection')
			->getApplications($id);
	}

	/**
	 * Invoke method after creation of connection
	 *
	 * @param Connection $connection
	 * @return void
	 */
	public function postPersist($connection)
	{
		$event = new ApplicationEvent();

		$event->setApplications($this->getApplications($connection->getId()));

		$this->dispatcher->dispatch(ApplicationEvents::POST_PERSIST, $event);
	}

	/**
	 * Invoke method after modification of connection
	 *
	 * @param Connection $connection
	 * @return void
	 */
	public function postUpdate($connection)
	{
		$event = new ApplicationEvent();

		$event->setApplications($this->getApplications($connection->getId()));

		$this->dispatcher->dispatch(ApplicationEvents::POST_UPDATE, $event);
	}

	/**
	 * Invoke method after deletion of connection
	 *
	 * @param Connection $connection
	 * @return void
	 */
	public function preRemove($connection)
	{
		$event = new ApplicationEvent();

		$event->setApplications($this->getApplications($connection->getId()));

        $this->dispatcher->dispatch(ApplicationEvents::PRE_REMOVE, $event);
	}

	/**
	 * Set Entity Manager
	 *
	 * @param EntityManager $entityManager
	 */
	public function setEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
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
                [
                    'label' => 'type'
                ],
                'choice',
                [
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
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add(
                'type',
                'choice',
                [
                    'label' => 'type',
                    'choices' => [
                        Connection::CONN_DB => $this->getTranslator()->trans('db_connection'),
                        Connection::CONN_HOST => $this->getTranslator()->trans('host_connection')
                    ]
                ]
            )
            ->add(
                'name',
                'text',
                [
                    'label' => 'name',
                    'editable' => true
                ]
            )
            ->add(
                'host',
                'text',
                [
                    'label' => 'host',
                    'editable' => true
                ]
            )
            ->add(
                'port',
                'integer',
                [
                    'label' => 'port',
                    'editable' => true
                ]
            )
            ->add(
                'userName',
                'text',
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
            ->add(
                'createdAt',
                'datetime',
                [
                    'label' => 'created_at',
                    'format' => 'm/d/Y'
                ]
            )
            ->add(
                'updatedAt',
                'datetime',
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