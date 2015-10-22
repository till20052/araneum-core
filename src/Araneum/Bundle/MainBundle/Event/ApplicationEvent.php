<?php

namespace Araneum\Bundle\MainBundle\Event;

use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ApplicationEvent
 * @package Araneum\Bundle\MainBundle\Event
 */
class ApplicationEvent extends Event
{
	/**
	 * @var ArrayCollection
	 */
	private $applications;

	/**
	 * Constructor of Application Event
	 */
	public function __construct()
	{
		$this->applications = new ArrayCollection();
	}

	/**
	 * Get applications
	 *
	 * @return ArrayCollection
	 */
	public function getApplications()
	{
		return $this->applications;
	}

	/**
	 * Add Application
	 *
	 * @param Application $application
	 */
	public function addApplication(Application $application)
	{
		$this->applications->add($application);
	}

	/**
	 * Set applications.
	 *
	 * @param Collection|ArrayCollection|PersistentCollection $applications
	 * @return ApplicationEvent $this
	 */
	public function setApplications(Collection $applications)
	{
		$this->applications->clear();

		/** @var Application $application */
		foreach ($applications as $application)
		{
			$this->addApplication($application);
		}

		return $this;
	}
}