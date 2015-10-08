<?php

namespace Araneum\Bundle\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ComponentOptionType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('key', 'text', ['label' => 'key'])
			->add('value', 'text', ['label' => 'value']);
	}

	public function getName()
	{
		return 'option';
	}
}