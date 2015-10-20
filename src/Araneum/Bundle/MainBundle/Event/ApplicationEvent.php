<?php

namespace Araneum\Bundle\MainBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ApplicationEvent
 * @package Araneum\Bundle\MainBundle\Event
 */
class ApplicationEvent extends Event
{
	/**
	 * @var array
	 */
	private $applications;

	/**
	 * Get applications
	 *
	 * @return array
	 */
	public function getApplications()
	{
		return $this->applications;
	}

	/**
	 * Set applications
	 *
	 * @param array $applications
	 * @return ApplicationEvent $this
	 */
	public function setApplications(array $applications)
	{
		$this->applications = $applications;

		return $this;
	}
}