<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
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
	}

	public function postUpdate(ApplicationEvent $applicationEvent)
	{
		var_dump($applicationEvent);die;
	}

	public function postRemove()
	{
	}
}