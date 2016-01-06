<?php

namespace Araneum\Bundle\AgentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AraneumAgentBundle
 *
 * @package Araneum\Bundle\AgentBundle
 */
class AraneumAgentBundle extends Bundle
{
    const EVENT_CUSTOMER_NEW            = 'araneum.agent.customer.new';
    const EVENT_CUSTOMER_RESET_PASSWORD = 'araneum.agent.customer.reset_password';
    const EVENT_LEAD_NEW                = 'araneum.agent.lead.new';
}
