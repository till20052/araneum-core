<?php

namespace Araneum\Bundle\CustomerBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CustomerAdmin extends Admin
{
	/**
	 * Configure customer form fields
	 *
	 * @param FormMapper $form
	 */
	protected function configureFormFields(FormMapper $form)
	{
		$form
			->add(
				'firstName',
				'text',
				[
					'label' => 'first_name',
					'required' => false
				]
			)
			->add(
				'lastName',
				'text',
				[
					'label' => 'last_name',
					'required' => false
				]
			)
			->add(
				'country',
				'text',
				[
					'label' => 'country',
					'required' => false
				]
			)
			->add('email', 'text', ['label' => 'email'])
			->add(
				'phone',
				'text',
				[
					'label' => 'phone',
					'required' => false
				]
			)
			->add(
				'callback',
				'checkbox',
				[
					'label' => 'callback',
					'required' => false
				]
			)
			->add(
				'deliveredAt',
				'sonata_type_date_picker',
				[
					'format' => 'MM/dd/yyyy',
					'label' => 'delivered_at',
					'required' => false
				]
			)
			->end()
			->with('application')
				->add('application', 'sonata_type_model', ['label' => 'application'])
			->end()
		;
	}

	/**
	 * Configure customer list fields
	 *
	 * @param ListMapper $list
	 */
	protected function configureListFields(ListMapper $list)
	{
		$list
			->addIdentifier('id')
			->add(
				'firstName',
				null,
				[
					'label' => 'first_name',
					'editable' => true
				]
			)
			->add(
				'lastName',
				null,
				[
					'label' => 'last_name',
					'editable' => true
				]
			)
			->add('email', null, ['label' => 'email'])
			->add(
				'phone',
				null,
				[
					'label' => 'phone',
					'editable' => true
				]
			)
			->add(
				'country',
				null,
				[
					'label' => 'country',
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
				'deliveredAt',
				'datetime',
				[
					'label' => 'delivered_at',
					'format' => 'm/d/Y',
					'editable' => true
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
							'delete' => [],
						]
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