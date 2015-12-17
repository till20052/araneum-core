<?php

namespace Araneum\Bundle\AgentBundle\Event;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CustomerEvent
 * @package Araneum\Bundle\AgentBundle\Event
 */
class CustomerEvent extends Event
{
    /**
     * @var Customer $customer
     */
    private $customer;

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer()
    {

        return $this->customer;
    }

    /**
     * Set customer
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer)
    {

        $this->customer = $customer;

        return $this;
    }
}
