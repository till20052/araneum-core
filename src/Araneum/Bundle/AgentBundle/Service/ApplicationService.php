<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\Application\ApplicationApiSenderService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Base\Service\RabbitMQ\ApiCustomerProducerService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class ApplicationOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class ApplicationService
{

    /**
     * @var ApiCustomerProducerService
     */
    protected $apiCustomerProducerService;

    /**
     * @var ApplicationApiSenderService
     */
    protected $applicationApiSenderService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * ApplicationOptionService constructor.
     *
     * @param ApiCustomerProducerService  $apiCustomerProducerService
     * @param ApplicationApiSenderService $applicationApiSenderService
     * @param EntityManager               $entityManager
     */
    public function __construct(
        ApiCustomerProducerService     $apiCustomerProducerService,
        ApplicationApiSenderService $applicationApiSenderService,
        EntityManager $entityManager
    ) {
        $this->apiCustomerProducerService = $apiCustomerProducerService;
        $this->applicationApiSenderService = $applicationApiSenderService;
        $this->entityManager = $entityManager;
    }

    /**
     * Send customers to application by url
     *
     * @param  Customer    $customer
     * @param  Application $application
     * @return \Guzzle\Http\Message\Response
     */
    public function createCustomer($customer, $url)
    {
        $data = [
            'customerData' => $this->getCustomerData($customer),
            'url' => $url,
            'customerId' => $customer->getId(),
        ];
        $this->apiCustomerProducerService->publish($data);
    }

    /**
     * Return customer data
     *
     * @param  Customer $customer
     * @return array
     */
    private function getCustomerData($customer)
    {
        return [
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'country' => $customer->getCountry(),
            'birthDate' => $customer->getBirthday(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'password' => $customer->getPassword(),
            'currency' => $customer->getCurrency(),
            'spotUserId' => $customer->getSpotId(),
        ];
    }
}
