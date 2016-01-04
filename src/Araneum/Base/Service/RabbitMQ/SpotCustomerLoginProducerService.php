<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class SpotCustomerProducerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class SpotCustomerLoginProducerService
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
     * @param Customer $customer
     * @param string   $routingKey
     * @param array    $additionalProperties
     * @return bool|string
     */
    public function publish(
        Customer $customer,
        $routingKey = 'sendToSpot',
        $additionalProperties = []
    ) {
        $application = $customer->getApplication();
        $msg = $this->getStdClass();
        $msg->data = [
            'email' => $customer->getEmail(),
            'password' => $customer->getPassword(),
        ];
        $msg->spoPublictUrl = $application->getSpotApiPublicUrl();
        $msg->log = [
            'action' => CustomerLog::ACTION_LOGIN,
            'customerId' => $customer->getId(),
            'applicationId' => $application->getId(),
        ];

        die(var_dump($msg));
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
