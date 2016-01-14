<?php

namespace Araneum\Bundle\AgentBundle\EventListener;

use Araneum\Bundle\AgentBundle\AraneumAgentBundle;
use Araneum\Bundle\AgentBundle\Event\CustomerEvent;
use Araneum\Bundle\AgentBundle\Event\LeadEvent;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;

/**
 * Class LeadListener
 *
 * @package Araneum\Bundle\AgentBundle\Event
 */
class LeadListener
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
     * @param LeadEvent $event
     */
    public function onCreate(LeadEvent $event)
    {
        $this->spotOptionService->leadCreate($event->getLead());
    }
}
