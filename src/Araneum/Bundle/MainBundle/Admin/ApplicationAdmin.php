<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ApplicationAdmin
 * @package Araneum\Bundle\MainBundle\Admin
 */
class ApplicationAdmin extends Admin
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Invoke method before creation of application
     *
     * @param Application $application
     * @return void
     */
    public function prePersist($application)
    {
        $application->setOwner(
            $this->container
                ->get('security.token_storage')
                ->getToken()
                ->getUser()
        );
    }

	/**
	 * Invoke method after creation of application
	 *
	 * @param Application $application
	 * @return void
	 */
	public function postPersist($application)
	{
		$this->dispatcher
			->dispatch(
				ApplicationEvent::POST_PERSIST,
				new ApplicationEvent($application)
			);
	}

	/**
	 * Invoke method after modification of application
	 *
     * @param Application $application
     * @return void
     */
    public function postUpdate($application)
    {
        $this->dispatcher
            ->dispatch(
                ApplicationEvent::POST_UPDATE,
                new ApplicationEvent($application)
            );
    }

	/**
	 * Invoke method after deletion of application
	 *
	 * @param Application $application
	 * @return void
	 */
	public function postRemove($application)
	{
		$this->dispatcher
			->dispatch(
				ApplicationEvent::POST_REMOVE,
				new ApplicationEvent($application)
			);
	}

    /**
     * Set Service Container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
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
     * Create/Update Form Configuration
     *
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('application')
            ->add(
                'type',
                'choice',
                [
                    'label' => 'type',
                    'required' => false,
                    'choices' => []
                ]
            )
            ->add('name', 'text', ['label' => 'name'])
            ->add('domain', 'text', ['label' => 'domain'])
            ->add(
                'aliases',
                'text',
                [
                    'label' => 'aliases',
                    'required' => false
                ]
            )
            ->add(
                'public',
                'checkbox',
                [
                    'label' => 'public',
                    'required' => false
                ]
            )
            ->add(
                'enabled',
                'checkbox',
                [
                    'label' => 'enabled',
                    'required' => false
                ]
            )
            ->add('template', 'text', ['label' => 'template'])
            ->end()
            ->with('cluster')
            ->add('cluster', 'sonata_type_model', ['label' => 'cluster'])
            ->end()
            ->with('database')
            ->add('db', 'sonata_type_model', ['label' => 'database'])
            ->end()
            ->with('locales')
            ->add(
                'locales',
                'sonata_type_model',
                [
                    'label' => 'locales',
                    'multiple' => true
                ]
            )
            ->end()
            ->with('components')
            ->add(
                'components',
                'sonata_type_model',
                [
                    'label' => 'components',
                    'multiple' => true
                ]
            )
            ->end();
    }

    /**
     * Fields of Application List Configuration
     *
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add('cluster', null, ['label' => 'cluster'])
            ->add('type', null, ['label' => 'type'])
            ->add('name', null, ['label' => 'name'])
            ->add('domain', null, ['label' => 'domain'])
            ->add('db', null, ['label' => 'database'])
            ->add(
                'public',
                null,
                [
                    'label' => 'public',
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
            ->add('locales', null, ['label' => 'locales'])
            ->add('owner', null, ['label' => 'owner'])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'status',
                    'choices' => [
                        Application::STATUS_UNDEFINED => 'undefined',
                        Application::STATUS_ONLINE => 'offline',
                        Application::STATUS_OFFLINE => 'online'
                    ]
                ]
            )
            ->add('template', null, ['label' => 'template'])
            ->add(
                'createdAt',
                'datetime',
                [
                    'label' => 'created_at',
                    'format' => 'm/d/Y'
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'label' => 'actions',
                    'actions' =>
                        [
                            'edit' => [],
                            'check_status_state' =>
                                [
                                    'template' => 'AraneumMainBundle:AdminAction:checkStatusApplication.html.twig'
                                ],
                            'delete' => [],
                        ]
                ]
            );
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('cluster', null, ['label' => 'cluster'])
            ->add('type', 'doctrine_orm_choice', ['label' => 'type'], 'choice', ['choices' => []])
            ->add('name', null, ['label' => 'name'])
            ->add('domain', null, ['label' => 'domain'])
            ->add('db', null, ['label' => 'database'])
            ->add('public', null, ['label' => 'public'])
            ->add('enabled', null, ['label' => 'enabled'])
            ->add('locales', null, ['label' => 'locales'])
            ->add('owner', null, ['label' => 'owner'])
            ->add(
                'status',
                'doctrine_orm_choice',
                ['label' => 'status'],
                'choice',
                [
                    'choices' =>
                        [
                            Application::STATUS_ONLINE => 'offline',
                            Application::STATUS_OFFLINE => 'online'
                        ]
                ]
            )
            ->add('template', null, ['label' => 'template'])
            ->add(
                'createdAt',
                'doctrine_orm_datetime_range',
                [
                    'label' => 'created_at',
                    'field_type' => 'sonata_type_datetime_range_picker'
                ],
                null,
                [
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy'
                ]
            );
    }
}