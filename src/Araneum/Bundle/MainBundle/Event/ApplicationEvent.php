<?php

namespace Araneum\Bundle\MainBundle\Event;

use Araneum\Bundle\MainBundle\Entity\Application;
use Symfony\Component\EventDispatcher\Event;

class ApplicationEvent extends Event
{
	const POST_PERSIST = 'araneum.main.application.event.post_persist';
	const POST_UPDATE = 'araneum.main.application.event.post_update';
	const POST_REMOVE = 'araneum.main.application.event.post_remove';

	/**
	 * @var Application
	 */
	private $application;

	/**
	 * Constructor of Application Event
	 *
	 * @param Application $application
	 */
	public function __construct(Application $application)
	{
		$this->application = $application;
	}

	/**
	 * Get application
	 *
	 * @return Application
	 */
	public function getApplication()
	{
		return $this->application;
	}
}