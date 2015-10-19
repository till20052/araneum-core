<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Event\AdminEventInterface;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Araneum\Bundle\MainBundle\Entity\Application;

class ApplicationEventListenerService
{
	/**
	 * @var RemoteApplicationManagerService
	 */
	private $remoteManager;

	/**
	 * Get applications
	 *
	 * @param Event|AdminEventInterface|ApplicationEvent| $event
	 * @return array
	 */
	private function getApplications(Event $event)
	{
		$applications = [];

		if($event instanceof ApplicationEvent)
			$applications[] = $event->getApplication();
		elseif($event instanceof AdminEventInterface)
			$applications = $event->getApplications();

		return $applications;
	}

	/**
	 * Constructor of ApplicationEventListenerService
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct($container)
	{
		$this->remoteManager = $container->get('araneum.main.application.remote_manager');
	}

	/**
	 * Invoke method after creation of applications
	 *
	 * @param Event $event
	 */
	public function postPersist(Event $event)
	{
		/** @var Application $application */
		foreach($this->getApplications($event) as $application){
			$this->remoteManager->create($application->getAppKey());
		}
	}

	/**
	 * Invoke method after modification of applications
	 *
	 * @param Event $event
	 */
	public function postUpdate(Event $event)
	{
		/** @var Application $application */
		foreach($this->getApplications($event) as $application){
			$this->remoteManager->update($application->getAppKey());
		}
	}

	/**
	 * Invoke method after deletion of applications
	 *
	 * @param Event $event
	 */
	public function postRemove(Event $event)
	{
		/** @var Application $application */
		foreach($this->getApplications($event) as $application){
			$this->remoteManager->remove($application->getAppKey());
		}
	}
}