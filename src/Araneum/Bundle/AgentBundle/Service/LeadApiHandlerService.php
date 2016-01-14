<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Exception\InvalidFormException;
use Araneum\Bundle\AgentBundle\AgentEvents;
use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\AgentBundle\Event\LeadEvent;
use Araneum\Bundle\AgentBundle\Form\Type\LeadType;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactory;

/**
 * Class LeadApiHandlerService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class LeadApiHandlerService
{
    /**
     * @var ApplicationManagerService
     */
    protected $applicationManager;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Service constructor
     *
     * @param EntityManager             $entityManager
     * @param FormFactory               $formFactory
     * @param ApplicationManagerService $applicationManager
     * @param EventDispatcherInterface  $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        FormFactory $formFactory,
        ApplicationManagerService $applicationManager,
        EventDispatcherInterface $dispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->applicationManager = $applicationManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Find list of leads by email and/or phone as optionality
     *
     * @param  array $filters
     * @return array
     */
    public function find(array $filters = [])
    {
        return $this->entityManager
            ->getRepository('AraneumAgentBundle:Lead')
            ->findByFilter($filters);
    }

    /**
     * Create lead
     *
     * @param  array $data
     * @return Lead
     */
    public function create(array $data)
    {
        $application = $this->applicationManager->findOneOr404(['appKey' => $data['appKey']]);
        $lead = new Lead();
        $lead->setApplication($application);
        $lead = $this->verifyDataByForm($data, $lead);

        $this->entityManager->persist($lead);
        $this->entityManager->flush();

        $event = new LeadEvent($lead);
        $this->dispatcher->dispatch(AgentEvents::LEAD_NEW, $event);

        return $lead;
    }

    /**
     * Check incoming data
     *
     * @param  array $data
     * @param Lead   $lead
     * @return Lead
     * @throws InvalidFormException
     */
    private function verifyDataByForm(array $data, Lead $lead)
    {
        $form = $this->formFactory
            ->create(new LeadType(), $lead)
            ->submit($data);

        if (!$form->isValid()) {
            throw new InvalidFormException($form, 'Not valid data');
        }

        return $lead;
    }
}
