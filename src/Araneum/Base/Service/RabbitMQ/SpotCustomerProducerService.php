<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class SpotCustomerProducerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class SpotCustomerProducerService
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
     * @param Customer $customer
     * @param string   $logAction
     * @param string   $routingKey           queue name
     * @param array    $additionalProperties
     * @return string|true
     */
    public function publish(
        $msgBody,
        Customer $customer,
        $logAction,
        $routingKey = '',
        $additionalProperties = []
    ) {
        $application = $customer->getApplication();
        $msg = $this->getStdClass();
        $msg->data = $msgBody;
        $msg->spotCredential = $application->getSpotCredential();
        $msg->log = [
            'action' => $logAction,
            'customerId' => $customer->getId(),
            'applicationId' => $application->getId(),
        ];

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
