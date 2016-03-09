<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Base\Service\Application\ApplicationApiSenderService;
use Doctrine\ORM\EntityManager;
use Guzzle\Service;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Guzzle\Http\Exception\RequestException;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;

/**
 * Class ApiCustomerProducerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class ApiCustomerConsumerService implements ConsumerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var ApplicationApiSenderService
     */
    private $applicationApiSenderService;
    /**
     * @var
     */
    private $msgConvertHelper;

    /**
     * Consumer constructor.
     *
     * @param ApplicationApiSenderService $applicationApiSenderService
     * @param MessageConversionHelper     $msgConvertHelper
     * @param EntityManager               $em
     */
    public function __construct(
        ApplicationApiSenderService $applicationApiSenderService,
        MessageConversionHelper $msgConvertHelper,
        EntityManager $em
    ) {
        $this->applicationApiSenderService = $applicationApiSenderService;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->em = $em;
    }

    /**
     * Receive message
     *
     * @param  AMQPMessage $message
     * @return string
     */
    public function execute(AMQPMessage $message)
    {
        $data = $this->msgConvertHelper->decodeMsg($message->body);
        $helper = ['url' => $data->credential->url, 'customerId' => $data->credential->customerId];
        $this->applicationApiSenderService->send((array) $data->data, (array) $helper);
    }
}
