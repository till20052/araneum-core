<?php

namespace Araneum\Bundle\AgentBundle\EventListener;

use Araneum\Bundle\AgentBundle\Event\CustomerEvent;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CustomerEvent
 *
 * @package Araneum\Bundle\AgentBundle\Event
 */
class CustomerListener extends Event
{
    protected $spotOptionService;

    /**
     * CustomerListener constructor.
     *
     * @param SpotOptionService $spotOptionService
     */
    public function __construct(SpotOptionService $spotOptionService)
    {
        $this->spotOptionService = $spotOptionService;
    }

    /**
     * Send data on spot by api with rabbitMQ
     *
     * @param CustomerEvent $event
     */
    public function onCreate(CustomerEvent $event)
    {
        $this->spotOptionService->customerCreate($event->getCustomer());
    }
}
