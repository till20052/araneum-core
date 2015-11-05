<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\MainBundle\ApplicationEvents;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
use Araneum\Bundle\MainBundle\Service\ApplicationEventListenerService;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class ApplicationEventListenerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    private $application;

    /**
     * Test Application Event Listener
     */
    public function testApplicationEvent()
    {
        $event = new ApplicationEvent();

        $event->addApplication($this->application);

        $this->dispatch($event);
    }

    /**
     * Dispatch all events
     *
     * @param Event $event
     */
    private function dispatch($event)
    {
        $this->dispatcher->dispatch(ApplicationEvents::POST_PERSIST, $event);
        $this->dispatcher->dispatch(ApplicationEvents::POST_UPDATE, $event);
        $this->dispatcher->dispatch(ApplicationEvents::PRE_REMOVE, $event);
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $remoteManager = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Service\RemoteApplicationManagerService')
            ->disableOriginalConstructor()
            ->getMock();

        foreach ([
                     'create',
                     'update',
                     'remove'
                 ] as $method) {
            $remoteManager->expects($this->once())
                ->method($method)
                ->with($this->equalTo(ApplicationFixtures::TEST_APP_APP_KEY))
                ->will($this->returnValue(true));
        }

        $listener = new ApplicationEventListenerService($remoteManager);

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
            ApplicationEvents::PRE_REMOVE,
            [
                $listener,
                'preRemove'
            ]
        );

        $this->application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');
        $this->application->expects($this->atLeastOnce())
            ->method('getAppKey')
            ->will($this->returnValue(ApplicationFixtures::TEST_APP_APP_KEY));
    }
}