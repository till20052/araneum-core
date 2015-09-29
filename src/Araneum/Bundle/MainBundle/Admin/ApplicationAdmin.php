<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class ApplicationAdmin extends Admin
{
	/**
	 * @param FormMapper $form
	 */
	protected function configureFormFields(FormMapper $form)
	{
		$form
			// name
			->add('name', 'text', ['label' => 'name'])
			// domain
			->add('domain', 'text', ['label' => 'domain'])
			// aliases
			->add('aliases', 'collection',
				[
					'type'         => 'text',
					'allow_add'    => true,
					'allow_delete' => true,
					'label' => 'Aliases',
					'options' =>
						[
							'label' => 'alias'
						]
				]
			)
			// public
			->add('public', 'checkbox',
				[
					'label' => 'Public',
					'required' => false
				]
			)
			// enabled
			->add('enabled', 'checkbox',
				[
					'label' => 'enabled',
					'required' => false
				]
			)
			// template
			->add('template', 'text', ['label' => 'template'])
			->end()
			// cluster
			->with('Cluster')
				->add('cluster', 'sonata_type_model', ['label' => 'cluster'])
			->end()
			// db
			->with('Database')
				->add('db', 'sonata_type_model', ['label' => 'database'])
			->end()
			// locale
			->with('Locale')
				->add('locale', 'sonata_type_model', ['label' => 'locale'])
			->end()
			// components
			->with('Components')
				->add('components', 'sonata_type_model',
					[
						'label' => 'locale',
						'multiple' => true
					]
				)
			->end()
			// owner
		;
	}
}