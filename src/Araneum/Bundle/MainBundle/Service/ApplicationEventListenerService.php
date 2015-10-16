<?php

namespace Araneum\Bundle\MainBundle\Service;

use Sonata\AdminBundle\Event\PersistenceEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApplicationEventListenerService
{
	/**
	 * @var RemoteApplicationManagerService
	 */
	private $remoteManager;

	public function __construct(ContainerInterface $container)
	{
		$this->remoteManager = $container->get('araneum.main.application.remote_manager');
	}

	public function postPersist()
	{
		$this->remoteManager->create();
	}

	public function postUpdate(PersistenceEvent $event)
	{
		var_dump($event->getObject());die;
		$this->remoteManager->update();
	}

	public function postRemove()
	{
		$this->remoteManager->remove();
	}
}