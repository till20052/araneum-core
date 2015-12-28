<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Bundle\MainBundle\Entity\Application;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class producerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class SpotProducerService
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
     * @param array       $msgBody
     * @param Application $application
     * @param string      $routingKey           queue name
     * @param array       $additionalProperties
     * @return string|true
     */
    public function publish($msgBody, Application $application, $routingKey = 'sendToSpot', $additionalProperties = [])
    {
        $msg = $this->getStdClass();
        $msg->data = $msgBody;
        $msg->spotCredential = $application->getSpotCredential();
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
