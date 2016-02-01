<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\Application\ApplicationApiSenderService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Base\Service\RabbitMQ\ApiCustomerProducerService;
use Doctrine\ORM\EntityManager;
/**
 * Class ApplicationOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class ApplicationOptionService
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
     * @param ApiCustomerProducerService     $apiCustomerProducerService
     * @param ApplicationApiSenderService    $applicationApiSenderService
     * @param EntityManager                  $entityManager
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
     * @param array         $customers
     * @param Application   $application
     * @param string   $url
     * @return \Guzzle\Http\Message\Response
     */
    public function sendCustomersToApplication($customers, $application, $url)
    {
        $data = [];
        foreach ($customers as $customer) {
            $data[] = $this->getCustomerData($customer);
        }

        return $this->apiCustomerProducerService->publish($data, $application->getDomain().$url);
    }


    /**
     * Return customer data
     *
     * @param Customer  $customer
     * @return array
     */
    private function getCustomerData($customer)
    {
        return [
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'country' => $customer->getCountry(),
            'birthday' => $customer->getBirthday(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'currency' => $customer->getCurrency(),
            'deliveredAt' => $customer->getDeliveredAt(),
            'createdAt' => $customer->getCreatedAt(),
            'updatedAt' => $customer->getUpdatedAt()
        ];
    }
}
