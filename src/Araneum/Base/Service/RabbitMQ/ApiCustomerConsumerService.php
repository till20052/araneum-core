<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Base\Service\Application\ApplicationApiSenderService;
use Doctrine\ORM\EntityManager;
use Guzzle\Service;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

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
    private $spotApiSenderService;
    /**
     * @var
     */
    private $msgConvertHelper;

    /**
     * Consumer constructor.
     *
     * @param ApplicationApiSenderService    $spotApiSenderService
     * @param MessageConversionHelper $msgConvertHelper
     * @param EntityManager           $em
     */
    public function __construct(
        ApplicationApiSenderService $spotApiSenderService,
        MessageConversionHelper $msgConvertHelper,
        EntityManager $em
    ) {
        $this->spotApiSenderService = $spotApiSenderService;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->em = $em;
    }

    /**
     * Receive message
     *
     * @param AMQPMessage $message
     * @return string
     */
    public function execute(AMQPMessage $message)
    {
        $data = $this->msgConvertHelper->decodeMsg($message->body);
        $spotResponse = $this->spotApiSenderService->send((array) $data->data, (array) $data->spotCredential);
    }
}
