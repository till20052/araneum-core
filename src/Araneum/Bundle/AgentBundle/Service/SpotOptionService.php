<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\RabbitMQ\SpotCustomerLoginProducerService;
use Araneum\Base\Service\Spot\SpotApiSenderService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\MainBundle\Entity\Application;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionService
{
    protected $customerLoginProducerService;
    protected $spotApiPublicUrlLogin;

    /**
     * SpotOptionService constructor.
     *
     * @param SpotCustomerLoginProducerService $customerLoginProducerService
     * @param                                  $spotApiPublicUrlLogin
     */
    public function __construct(SpotCustomerLoginProducerService $customerLoginProducerService, $spotApiPublicUrlLogin)
    {
        $this->spotApiPublicUrlLogin = $spotApiPublicUrlLogin;
        $this->customerLoginProducerService = $customerLoginProducerService;
    }

    /**
     * SpotOption Login
     *
     * @param Customer $customer
     * @return array|bool
     */
    public function login(Customer $customer)
    {
        return $this->customerLoginProducerService->publish($customer);

        $requestData = [
            'email' => $email,
            'password' => $password,
        ];
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
}
