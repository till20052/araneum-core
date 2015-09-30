<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Application;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApplicationAdmin extends Admin
{
	/**
	 * Create/Update Form Configuration
	 *
	 * @param FormMapper $form
	 */
	protected function configureFormFields(FormMapper $form)
	{
		$form
			->with('application')
				->add('type', 'choice',
					[
						'label' => 'type',
						'required' => false,
						'choices' => []
					]
				)
				->add('name', 'text', ['label' => 'name'])
				->add('domain', 'text', ['label' => 'domain'])
				->add('aliases', 'text',
					[
						'label' => 'aliases',
						'required' => false
					]
				)
				->add('public', 'checkbox',
					[
						'label' => 'public',
						'required' => false
					]
				)
				->add('enabled', 'checkbox',
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
			->with('locale')
				->add('locale', 'sonata_type_model', ['label' => 'locale'])
			->end()
			->with('components')
				->add('components', 'sonata_type_model',
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
			->add('id', null, ['label' => 'id'])
			->add('cluster', null, ['label' => 'cluster'])
			->add('type', null, ['label' => 'type'])
			->add('name', null, ['label' => 'name'])
			->add('domain', null, ['label' => 'domain'])
			->add('db', null, ['label' => 'database'])
			->add('public', null,
				[
					'label' => 'public',
					'editable' => true
				]
			)
			->add('enabled', null,
				[
					'label' => 'enabled',
					'editable' => true
				]
			)
			->add('locale', null, ['label' => 'locale'])
			->add('owner', null, ['label' => 'owner'])
			->add('status', 'choice',
				[
					'label' => 'status',
					'choices' => [
						'' => 'undefined',
						0 => 'offline',
						1 => 'online'
					]
				]
			)
			->add('template', null, ['label' => 'template'])
			->add('createdAt', 'datetime',
				[
					'label' => 'created_at',
					'format' => 'm/d/Y'
				]
			)
			->add('_action', 'actions',
				[
					'label' => 'actions',
					'actions' =>
						[
							'edit' => [],
							'check_status_state' =>
								[
									'template' => 'AraneumMainBundle:AdminApplication:check_status_state.html.twig'
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
			->add('locale', null, ['label' => 'locale'])
			->add('owner', null, ['label' => 'owner'])
			->add('status', 'doctrine_orm_choice', ['label' => 'status'], 'choice',
				['choices' =>
					[
						0 => 'offline',
						1 => 'online'
					]
				]
			)
			->add('template', null, ['label' => 'template'])
			->add('createdAt', 'doctrine_orm_datetime_range',
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

	/**
	 * Set Application Owner before insert
	 *
	 * @param Application $application
	 * @return true
	 */
	public function prePersist($application)
	{
		$application->setOwner(
			$this
				->getContainer()
				->get('security.context')
				->getToken()
				->getUser()
		);

		return true;
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
	 * Get Service Container
	 *
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}
}