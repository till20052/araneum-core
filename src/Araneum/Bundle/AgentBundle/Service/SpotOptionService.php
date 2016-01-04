<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\RabbitMQ\SpotCustomerLoginProducerService;
use Araneum\Base\Service\RabbitMQ\SpotCustomerProducerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Base\Service\Spot\SpotApiSenderService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionService
{

    protected $customerLoginProducerService;
    /**
     * @var SpotCustomerProducerService
     */
    protected $spotProducerService;

    /**
     * @var SpotOptionService
     */
    protected $spotApiSenderService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * SpotOptionService constructor.
     *
     * @param SpotCustomerLoginProducerService $customerLoginProducerService
     * @param SpotCustomerProducerService $spotProducerService
     * @param SpotApiSenderService        $spotApiSenderService
     * @param EntityManager               $entityManager
     */
    public function __construct(
        SpotCustomerLoginProducerService $customerLoginProducerService,
        SpotCustomerProducerService $spotProducerService,
        SpotApiSenderService $spotApiSenderService,
        EntityManager $entityManager
    ) {
        $this->customerLoginProducerService = $customerLoginProducerService;
        $this->spotProducerService = $spotProducerService;
        $this->spotApiSenderService = $spotApiSenderService;
        $this->entityManager = $entityManager;
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

    }

    /**
     * Reset Customer Password on SpotOption
     *
     * @param Customer $customer
     * @return bool
     */
    public function customerResetPassword(Customer $customer)
    {
        $customerData = [
            'MODULE' => 'Customer',
            'COMMAND' => 'edit',
            'customerId' => $customer->getSpotId(),
            'password' => $customer->getPassword(),
        ];

        return $this->spotProducerService->publish($customerData, $customer, CustomerLog::ACTION_RESET_PASSWORD);
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
            'password' => $customer->getPassword(),
            'Phone' => $customer->getPhone(),
            'Country' => $customer->getCountry(),
            'currency' => $customer->getCurrency(),
        ];

        if ($customer->getBirthday()) {
            $customerData['birthday'] = $customer->getBirthday()->format('Y-m-d');
        }

        return $this->spotProducerService->publish($customerData, $customer, CustomerLog::ACTION_CREATE);
    }

    /**
     * Get countries method
     *
     * @param string $appKey
     * @return mixed
     * @throws NotFoundHttpException in case if can't found by application appKey
     */
    public function getCountries($appKey)
    {
        /** @var Application $application */
        $application = $this->entityManager
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByAppKey($appKey);

        if (empty($application)) {
            throw new NotFoundHttpException('Not Application found for this appKey', null, Response::HTTP_NOT_FOUND);
        }

        return $this->spotApiSenderService->get(
            [
                'MODULE' => 'Country',
                'COMMAND' => 'view',
            ],
            $application->getSpotCredential()
        );
    }
}
