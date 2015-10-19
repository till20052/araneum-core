<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
use Araneum\Bundle\MainBundle\Event\ClusterEvent;
use Araneum\Bundle\MainBundle\Event\ComponentEvent;
use Araneum\Bundle\MainBundle\Event\ConnectionEvent;
use Araneum\Bundle\MainBundle\Service\ApplicationEventListenerService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class ApplicationEventListenerServiceTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var EventDispatcher
	 */
	private $dispatcher;

	private $application;

	private $cluster;

	/**
	 * Dispatch all events
	 *
	 * @param Event $event
	 */
	private function dispatch($event)
	{
		$this->dispatcher->dispatch(ApplicationEvent::POST_PERSIST, $event);
		$this->dispatcher->dispatch(ApplicationEvent::POST_UPDATE, $event);
		$this->dispatcher->dispatch(ApplicationEvent::POST_REMOVE, $event);
	}

	protected function setUp()
	{
		$remoteManager = $this->getMock('\Araneum\Bundle\MainBundle\Service\RemoteApplicationManagerService');

		foreach(['create', 'update', 'remove'] as $method)
		{
			$remoteManager->expects($this->once())
				->method($method)
				->with($this->equalTo(ApplicationFixtures::TEST_APP_API_KEY))
				->will($this->returnValue(true));
		}

		$container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
		$container->expects($this->atLeastOnce())
			->method('get')
			->with($this->equalTo('araneum.main.application.remote_manager'))
			->will($this->returnValue($remoteManager));

		$listener = new ApplicationEventListenerService($container);

		$this->dispatcher = new EventDispatcher();
		$this->dispatcher->addListener(
			ApplicationEvent::POST_PERSIST,
			[
				$listener,
				'postPersist'
			]
		);
		$this->dispatcher->addListener(
			ApplicationEvent::POST_UPDATE,
			[
				$listener,
				'postUpdate'
			]
		);
		$this->dispatcher->addListener(
			ApplicationEvent::POST_REMOVE,
			[
				$listener,
				'postRemove'
			]
		);

		$this->application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');
		$this->application->expects($this->atLeastOnce())
			->method('getApiKey')
			->will($this->returnValue(ApplicationFixtures::TEST_APP_API_KEY));

		$this->cluster = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Cluster');
		$this->cluster->expects($this->any())
			->method('getApplications')
			->will($this->returnValue(new ArrayCollection([$this->application])));
	}

	public function testApplicationEvent()
	{
		$this->dispatch(new ApplicationEvent($this->application));
	}

	public function testClusterEvent()
	{
		$this->dispatch(new ClusterEvent($this->cluster));
	}

	public function testComponentEvent()
	{
		$component = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Component');
		$component->expects($this->atLeastOnce())
			->method('getApplications')
			->will($this->returnValue(new ArrayCollection([$this->application])));

		$this->dispatch(new ComponentEvent($component));
	}

	public function testConnectionEvent()
	{
		$connection = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Connection');
		$connection->expects($this->atLeastOnce())
			->method('getClusters')
			->will($this->returnValue(new ArrayCollection([$this->cluster])));

		$this->dispatch(new ConnectionEvent($connection));
	}
}