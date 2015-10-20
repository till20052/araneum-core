<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\MainBundle\ApplicationEvents;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
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
		$this->dispatcher->dispatch(ApplicationEvents::POST_PERSIST, $event);
		$this->dispatcher->dispatch(ApplicationEvents::POST_UPDATE, $event);
		$this->dispatcher->dispatch(ApplicationEvents::POST_REMOVE, $event);
	}

	/**
	 * @inheritdoc
	 */
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
			ApplicationEvents::POST_PERSIST,
			[
				$listener,
				'postPersist'
			]
		);
		$this->dispatcher->addListener(
			ApplicationEvents::POST_UPDATE,
			[
				$listener,
				'postUpdate'
			]
		);
		$this->dispatcher->addListener(
			ApplicationEvents::POST_REMOVE,
			[
				$listener,
				'postRemove'
			]
		);

		$this->application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');
		$this->application->expects($this->atLeastOnce())
			->method('getAppKey')
			->will($this->returnValue(ApplicationFixtures::TEST_APP_API_KEY));
	}

	/**
	 * Test Application Event Listener
	 */
	public function testApplicationEvent()
	{
		$event = new ApplicationEvent();

		$event->setApplications(
			[$this->application]
		);

		$this->dispatch($event);
	}
}