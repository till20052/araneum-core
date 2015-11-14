<?php

namespace Araneum\Bundle\AgentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CustomerAdmin extends Admin
{
	/**
	 * Configure routes
	 *
	 * @param RouteCollection $collection
	 */
	protected function configureRoutes(RouteCollection $collection)
	{
		$collection->remove('araneum.agent.admin.customer.edit');
		$collection->remove('araneum.agent.admin.customer.create');
		$collection->remove('araneum.agent.admin.customer.delete');
	}

	/**
	 * Configure customer list fields
	 *
	 * @param ListMapper $list
	 */
	protected function configureListFields(ListMapper $list)
	{
		$list
			->addIdentifier('id', null, ['label' => 'id'])
			->add(
				'application',
				'sonata_type_model',
				[
					'multiple' => true,
					'by_reference' => false,
					'required' => false
				]
			)
			->add(
				'firstName',
				null,
				[
					'label' => 'first_name'
				]
			)
			->add(
				'lastName',
				null,
				[
					'label' => 'last_name'
				]
			)
			->add('email', null, ['label' => 'email'])
			->add(
				'phone',
				null,
				[
					'label' => 'phone'
				]
			)
			->add(
				'country',
				null,
				[
					'label' => 'country'
				]
			)
			->add(
				'currency',
				null,
				[
					'label'	=> 'currency'
				]
			)
			->add(
				'callback',
				null,
				[
					'label' => 'callback'
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
				'deliveredAt',
				'datetime',
				[
					'label' => 'delivered_at',
					'format' => 'm/d/Y'
				]
			);
	}

	/**
	 * Configure customer filters
	 *
	 * @param DatagridMapper $filter
	 */
	protected function configureDatagridFilters(DatagridMapper $filter)
	{
		$filter
			->add('application', null, ['label' => 'application'])
			->add('firstName', null, ['label' => 'first_name'])
			->add('lastName', null, ['label' => 'last_name'])
			->add('email', null, ['label' => 'email'])
			->add('phone', null, ['label' => 'phone'])
			->add('currency', null, ['label' => 'currency'])
			->add('country', null, ['label' => 'country'])
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
			)
			->add(
				'deliveredAt',
				'doctrine_orm_datetime_range',
				[
					'label' => 'delivered_at',
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