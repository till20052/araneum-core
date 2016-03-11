<?php

namespace Araneum\Base\Service\RabbitMQ;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class ProducerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class ProducerService
{
    /**
     * @var Producer
     */
    private $producer;
    /**
     * @var MessageConversionHelper
     */
    private $msgConvertHelper;
    /**
     * @var
     */
    private $queueExpiration;

    /**
     * producerService constructor.
     *
     * @param Producer                $producer
     * @param MessageConversionHelper $msgConvertHelper
     * @param string                  $queueExpiration
     */
    public function __construct(Producer $producer, MessageConversionHelper $msgConvertHelper, $queueExpiration)
    {
        $this->producer = $producer;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->queueExpiration = $queueExpiration;
    }

    /**
     * Send msg to queue.
     *
     * @param mixed  $msgBody
     * @param mixed  $credential
     * @param string $routingKey
     * @param array  $additionalProperties
     * @return bool|string
     */
    public function publish(
        $msgBody,
        array $credential,
        $routingKey = '',
        $additionalProperties = []
    ) {
        $msg = $this->getStdClass();
        $msg->data = $msgBody;
        $msg->credential = $credential;

        try {
            $this->producer->publish(
                $this->msgConvertHelper->encodeMsg($msg),
                $routingKey,
                array_merge($additionalProperties, ['expiration' => $this->queueExpiration])
            );

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get \stdClass object
     *
     * @return \stdClass
     */
    private function getStdClass()
    {
        return new \stdClass();
    }
}
