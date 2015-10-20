<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Araneum\Bundle\MainBundle\Entity\Application;

class ApplicationEventListenerService
{
	/**
	 * @var RemoteApplicationManagerService
	 */
	private $remoteManager;

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
	 * @param ApplicationEvent $event
	 */
	public function postPersist(ApplicationEvent $event)
	{
		/** @var Application $application */
		foreach($event->getApplications() as $application){
			$this->remoteManager->create($application->getAppKey());
		}
	}

	/**
	 * Invoke method after modification of applications
	 *
	 * @param ApplicationEvent $event
	 */
	public function postUpdate(ApplicationEvent $event)
	{
		/** @var Application $application */
		foreach($event->getApplications() as $application){
			$this->remoteManager->update($application->getAppKey());
		}
	}

	/**
	 * Invoke method after deletion of applications
	 *
	 * @param ApplicationEvent $event
	 */
	public function postRemove(ApplicationEvent $event)
	{
		/** @var Application $application */
		foreach($event->getApplications() as $application){
			$this->remoteManager->remove($application->getAppKey());
		}
	}
}