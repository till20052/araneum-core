<?php

namespace Araneum\Base\Service\RabbitMQ;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class ApiCustomerProducerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class ApiCustomerProducerService
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
     * Return true
     *
     * @param  int    $customerId
     * @param  array  $msgBody
     * @param  array  $application
     * @param  string $routingKey           queue name
     * @param  array  $additionalProperties
     * @return string|true
     */
    public function publish(
        $customerId,
        $msgBody,
        $application,
        $routingKey = '',
        $additionalProperties = []
    ) {
        $msg = $this->getStdClass();
        $msg->data = $msgBody;
        $msg->url = $application['url'];
        $msg->appId = $application['id'];
        $msg->customerId = $customerId;
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
