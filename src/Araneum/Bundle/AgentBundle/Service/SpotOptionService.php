<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\RabbitMQ\SpotProducerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionService
{
    /**
     * @var SpotProducerService
     */
    protected $spotOptionService;

    /**
     * SpotOptionService constructor.
     *
     * @param SpotProducerService $spotOptionService
     */
    public function __construct(SpotProducerService $spotOptionService)
    {
        $this->spotOptionService = $spotOptionService;
    }

    /**
     * SpotOption Login
     *
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function login($login, $password)
    {
        $login = null;
        $password = null;

        return true;
    }

    /**
     * Reset Customer Password on SpotOption
     *
     * @param string $login
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function resetPassword($login, $currentPassword, $newPassword)
    {
        $login = null;
        $currentPassword = null;
        $newPassword = null;

        return true;
    }

    /**
     * Send customer creation data to SpotOption with RabbitMQ
     *
     * @param Customer $customer
     * @return string|true
     */
    public function customerCreate(Customer $customer)
    {
        $customerData = [
            'MODULE' => 'Customer',
            'COMMAND' => 'add',
            'FirstName' => $customer->getFirstName(),
            'LastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'password' => '123456',
            'Phone' => $customer->getPhone(),
            'Country' => 123,
            'currency' => $customer->getCurrency(),
            'birthday' => '1980-07-21',
        ];

        return $this->spotOptionService->publish($customerData, $customer->getApplication());
    }
}
