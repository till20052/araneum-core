<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Event\ApplicationEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Araneum\Bundle\MainBundle\Entity\Application;

/**
 * Class ApplicationEventListenerService
 *
 * @package Araneum\Bundle\MainBundle\Service
 */
class ApplicationEventListenerService
{
    /**
     * @var RemoteApplicationManagerService
     */
    private $remoteManager;

    /**
     * Constructor of ApplicationEventListenerService
     *
     * @param RemoteApplicationManagerService $remoteManager
     */
    public function __construct(RemoteApplicationManagerService $remoteManager)
    {
        $this->remoteManager = $remoteManager;
    }

    /**
     * Invoke method after creation of applications
     *
     * @param ApplicationEvent $event
     */
    public function postPersist(ApplicationEvent $event)
    {
        /** @var Application $application */
        foreach ($event->getApplications() as $application) {
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
        foreach ($event->getApplications() as $application) {
            $this->remoteManager->update($application->getAppKey());
        }
    }

    /**
     * Invoke method after deletion of applications
     *
     * @param ApplicationEvent $event
     */
    public function preRemove(ApplicationEvent $event)
    {
        /** @var Application $application */
        foreach ($event->getApplications() as $application) {
            $this->remoteManager->remove($application->getAppKey());
        }
    }
}
