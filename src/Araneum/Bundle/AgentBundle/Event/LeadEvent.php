<?php

namespace Araneum\Bundle\AgentBundle\Event;

use Araneum\Bundle\AgentBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class LeadEvent
 *
 * @package Araneum\Bundle\AgentBundle\Event
 */
class LeadEvent extends Event
{
    /**
     * @var Lead $lead
     */
    private $lead;

    /**
     * LeadEvent constructor.
     *
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Get Lead
     *
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * Set Lead
     *
     * @param Lead $lead
     * @return LeadEvent
     */
    public function setLead(Lead $lead)
    {
        $this->lead = $lead;

        return $this;
    }
}
