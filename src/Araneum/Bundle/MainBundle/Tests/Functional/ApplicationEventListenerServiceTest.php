<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\ApplicationEvents;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;

/**
 * Class ApplicationEventListenerServiceTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional
 */
class ApplicationEventListenerServiceTest extends BaseController
{
    /**
     *
     */
    public function testApplicationEventListener()
    {
        $client = static::createClient();
        $dispatcher = $client->getContainer()
            ->get('event_dispatcher');

        $this->assertTrue(count($dispatcher->getListeners(ApplicationEvents::POST_PERSIST)) > 0);
        $this->assertTrue(count($dispatcher->getListeners(ApplicationEvents::POST_UPDATE)) > 0);
        $this->assertTrue(count($dispatcher->getListeners(ApplicationEvents::PRE_REMOVE)) > 0);
    }
}
