<?php

namespace Araneum\Bundle\AgentBundle\Service\Applications;


use Araneum\Base\Service\RabbitMQ\ApiCustomerProducerService;
use Araneum\Base\Service\RabbitMQ\ApiCustomerConsumerService;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Doctrine\ORM\EntityManager;
use fixtures\App;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ApplicationsApiService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class ApplicationsApiService
{

    protected $customerLoginProducerService;
    /**
     * @var SpotCustomerProducerService
     */
    protected $spotCustomerProducerService;

    /**
     * @var ApplicationsApiService
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
     * @param SpotCustomerProducerService      $spotCustomerProducerService
     * @param SpotCustomerLoginProducerService $customerLoginProducerService
     * @param SpotProducerService              $spotProducerService
     * @param SpotApiSenderService             $spotApiSenderService
     * @param EntityManager                    $entityManager
     */
    public function __construct(
        SpotCustomerProducerService $spotCustomerProducerService,
        SpotCustomerLoginProducerService $customerLoginProducerService,
        SpotProducerService $spotProducerService,
        SpotApiSenderService $spotApiSenderService,
        EntityManager $entityManager
    ) {
        $this->customerLoginProducerService = $customerLoginProducerService;
        $this->spotCustomerProducerService = $spotCustomerProducerService;
        $this->spotProducerService = $spotProducerService;
        $this->spotApiSenderService = $spotApiSenderService;
        $this->entityManager = $entityManager;
    }
}
