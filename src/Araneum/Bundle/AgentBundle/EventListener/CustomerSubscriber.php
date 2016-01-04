<?php

namespace Araneum\Bundle\AgentBundle\EventListener;

use Araneum\Bundle\AgentBundle\AraneumAgentBundle;
use Araneum\Bundle\AgentBundle\Event\CustomerEvent;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CustomerSubscriber
 *
 * @package Araneum\Bundle\AgentBundle\Event
 */
class CustomerSubscriber implements EventSubscriberInterface
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
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            AraneumAgentBundle::EVENT_CUSTOMER_NEW => 'onCreate',
            AraneumAgentBundle::EVENT_CUSTOMER_RESET_PASSWORD => 'onResetPassword',
        ];
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

    /**
     * Send data on spot by api with rabbitMQ
     *
     * @param CustomerEvent $event
     */
    public function onResetPassword(CustomerEvent $event)
    {
        $this->spotOptionService->customerResetPassword($event->getCustomer());
    }
}
