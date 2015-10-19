<?php

namespace Araneum\Bundle\MainBundle\Event;

use Araneum\Bundle\MainBundle\Entity\Component;
use Symfony\Component\EventDispatcher\Event;

class ComponentEvent extends Event implements AdminEventInterface
{
	/**
	 * @var Component
	 */
	private $component;

	/**
	 * Constructor of Component Event
	 *
	 * @param Component $component
	 */
	public function __construct(Component $component)
	{
		$this->component = $component;
	}

	/**
	 * Get component applications
	 *
	 * @return array
	 */
	public function getApplications()
	{
		return $this->component->getApplications()->toArray();
	}
}