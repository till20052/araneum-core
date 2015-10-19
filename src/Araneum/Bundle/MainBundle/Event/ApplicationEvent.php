<?php

namespace Araneum\Bundle\MainBundle\Event;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Component;
use Symfony\Component\EventDispatcher\Event;

class ApplicationEvent extends Event
{
	/**
	 * @var Application
	 */
	private $application;

	/**
	 * Constructor of Application Event
	 *
	 * @param Application|Component $object
	 */
	public function __construct($object)
	{

	}
}