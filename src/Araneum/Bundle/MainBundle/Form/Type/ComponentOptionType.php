<?php

namespace Araneum\Bundle\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ComponentOptionType
 * @package Araneum\Bundle\MainBundle\Form\Type
 */
class ComponentOptionType extends AbstractType
{
	/**
	 * Build token of component options
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('key', 'text', ['label' => 'key'])
			->add('value', 'text', ['label' => 'value']);
	}

	/**
	 * Get token name of component options
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'araneum_main_form_component_option';
	}
}