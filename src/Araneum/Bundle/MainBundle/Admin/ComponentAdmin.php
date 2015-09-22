<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ComponentAdmin extends Admin
{
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('name', 'text', [
				'label' => 'Name'
			])
			->add('description', 'textarea', [
				'label' => 'Description'
			])
			->add('enabled', 'checkbox', [
				'label' => 'Enabled',
				'required' => false
			])
			->add('default', 'checkbox', [
				'label' => 'Default',
				'required' => false
			])
		;
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('name', null, [
				'label' => 'Name'
			])
			->add('description', null, [
				'label' => 'Description'
			])
			->add('enabled', null, [
				'label' => 'Enabled'
			])
			->add('default', null, [
				'label' => 'Default'
			])
			->add('createdAt', null, [
				'label' => 'Created At'
			])
		;
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->addIdentifier('id', null, [
				'label' => 'ID'
			])
			->add('name', null, [
				'editable' => true
			])
			->add('description', null, [
				'editable' => true
			])
			->add('enabled', null, [
				'label' => 'Enabled',
				'editable' => true
			])
			->add('default', null, [
				'lable' => 'Default',
				'editable' => true
			])
			->add('createdAt', null, [
				'label' => 'Created At'
			])
			->add('_action', 'actions', [
				'actions' => [
					'edit' => [],
					'delete' => []
				]
			])
		;
	}
}