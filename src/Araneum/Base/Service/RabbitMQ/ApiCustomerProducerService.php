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
     * @param array    $msgBody
     * @param string   $logAction
     * @param string   $url
     * @param string   $routingKey           queue name
     * @return string|true
     */
    public function publish(
        $msgBody,
        $url,
        $logAction,
        $routingKey = 'sendToApi'
    ) {
        $msg = $this->getStdClass();
        $msg->data = $msgBody;
        $msg->url = $url;

        try {
            $this->producer->publish(
                $this->msgConvertHelper->encodeMsg($msg),
                $routingKey,
                array_merge(['url'=> $url], ['expiration' => $this->queueExpiration])
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
