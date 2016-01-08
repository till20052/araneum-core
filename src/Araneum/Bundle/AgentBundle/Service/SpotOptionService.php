<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\RabbitMQ\SpotCustomerProducerService;
use Araneum\Base\Service\RabbitMQ\SpotProducerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\AgentBundle\Entity\Lead;
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
    /**
     * @var SpotCustomerProducerService
     */
    protected $spotCustomerProducerService;

    /**
     * @var SpotOptionService
     */
    protected $spotApiSenderService;

    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var SpotProducerService
     */
    protected $spotProducerService;

    /**
     * SpotOptionService constructor.
     *
     * @param SpotCustomerProducerService $spotCustomerProducerService
     * @param SpotProducerService         $spotProducerService
     * @param SpotApiSenderService        $spotApiSenderService
     * @param EntityManager               $entityManager
     */
    public function __construct(
        SpotCustomerProducerService $spotCustomerProducerService,
        SpotProducerService $spotProducerService,
        SpotApiSenderService $spotApiSenderService,
        EntityManager $entityManager
    ) {
        $this->spotCustomerProducerService = $spotCustomerProducerService;
        $this->spotProducerService = $spotProducerService;
        $this->spotApiSenderService = $spotApiSenderService;
        $this->entityManager = $entityManager;
    }

    /**
     * SpotOption Login
     *
     * @param  string $login
     * @param  string $password
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

        return $this->spotCustomerProducerService->publish(
            $customerData,
            $customer,
            CustomerLog::ACTION_RESET_PASSWORD
        );
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

        return $this->spotCustomerProducerService->publish($customerData, $customer, CustomerLog::ACTION_CREATE);
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

    /**
     * Send Lead creation data to SpotOption with RabbitMQ
     *
     * @param Lead $lead
     * @return bool|string
     */
    public function leadCreate(Lead $lead)
    {
        $leadData = [
            'MODULE' => 'Lead',
            'COMMAND' => 'add',
            'FirstName' => $lead->getFirstName(),
            'LastName' => $lead->getLastName(),
            'Country' => $lead->getCountry(),
            'Phone' => $lead->getPhone(),
            'email' => $lead->getEmail(),
        ];

        return $this->spotProducerService->publish($leadData, $lead->getApplication()->getSpotCredential());
    }
}
