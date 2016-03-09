<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\RabbitMQ\ProducerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Base\Service\Spot\SpotApiSenderService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionService
{

    protected $customerLoginProducerService;
    /**
     * @var ProducerService
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
     * @var ProducerService
     */
    protected $spotProducerService;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * SpotOptionService constructor.
     *
     * @param ProducerService      $spotCustomerProducerService
     * @param ProducerService      $customerLoginProducerService
     * @param ProducerService      $spotProducerService
     * @param SpotApiSenderService $spotApiSenderService
     * @param EntityManager        $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ProducerService $spotCustomerProducerService,
        ProducerService $customerLoginProducerService,
        ProducerService $spotProducerService,
        SpotApiSenderService $spotApiSenderService,
        EntityManager $entityManager,
        SerializerInterface $serializer
    ) {
        $this->customerLoginProducerService = $customerLoginProducerService;
        $this->spotCustomerProducerService = $spotCustomerProducerService;
        $this->spotProducerService = $spotProducerService;
        $this->spotApiSenderService = $spotApiSenderService;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * SpotOption Login
     *
     * @param  Customer $customer
     * @return array|bool
     */
    public function login(Customer $customer)
    {
        $body = $this->serializer->serialize(
            $customer,
            'json',
            SerializationContext::create()->setGroups(['rabbitMQ'])
        );

        return $this->customerLoginProducerService->publish($body, '');
    }

    /**
     * Reset Customer Password on SpotOption
     *
     * @param  Customer $customer
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
        $application = $customer->getApplication();

        $credentials = [
            'spotCredentials' => $customer->getApplication()->getSpotCredential(),
            'log' => [
                'action' => CustomerLog::ACTION_RESET_PASSWORD,
                'customerId' => $customer->getId(),
                'applicationId' => $application->getId(),
            ],
        ];
        return $this->spotCustomerProducerService->publish($customerData, $credentials);
    }

    /**
     * Send customer creation data to SpotOption with RabbitMQ
     *
     * @param  Customer $customer
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
     * @param  string $appKey
     * @return mixed
     * @throws NotFoundHttpException in case if can't found by application appKey
     */
    public function getCountries($appKey)
    {
        /**
         * @var Application $application
         */
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
     * @param  Lead $lead
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

    /**
     * Get customers from spot by filter
     *
     * @param  Application $application
     * @param  array       $filterOptions
     * @return \Guzzle\Http\Message\Response
     */
    public function getCustomersByFilter($application, $filterOptions)
    {
        $data = [
            'MODULE' => 'Customer',
            'COMMAND' => 'view',
            'FILTER' => $filterOptions,
        ];

        return $this->spotApiSenderService->send($data, $application->getSpotCredential());
    }
}
