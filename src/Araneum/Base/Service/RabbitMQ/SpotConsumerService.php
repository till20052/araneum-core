<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Service;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class SpotConsumerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class SpotConsumerService implements ConsumerInterface
{
    /**
     * @var SpotApiSenderService
     */
    private $spotApiSenderService;
    /**
     * @var
     */
    private $msgConvertHelper;

    /**
     * Consumer constructor.
     *
     * @param SpotApiSenderService    $spotApiSenderService
     * @param MessageConversionHelper $msgConvertHelper
     */
    public function __construct(
        SpotApiSenderService $spotApiSenderService,
        MessageConversionHelper $msgConvertHelper
    ) {
        $this->spotApiSenderService = $spotApiSenderService;
        $this->msgConvertHelper = $msgConvertHelper;
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
        $response = $this->spotApiSenderService->send((array) $data->data, (array) $data->spotCredential);
        echo $response->getBody(true);
    }
}
