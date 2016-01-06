<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class SpotCustomerProducerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class SpotCustomerLoginProducerService
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;
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
     * ProducerService constructor.
     *
     * @param Producer                $producer
     * @param MessageConversionHelper $msgConvertHelper
     * @param SerializerInterface     $serializer
     * @param string                  $queueExpiration
     */
    public function __construct(
        Producer $producer,
        MessageConversionHelper $msgConvertHelper,
        SerializerInterface $serializer,
        $queueExpiration
    ) {
        $this->producer = $producer;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->serializer = $serializer;
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
        $routingKey = '',
        $additionalProperties = []
    ) {
        $msg = $this->getStdClass();
        $msg->data = $this->serializer->serialize(
            $customer,
            'json',
            SerializationContext::create()->setGroups(['rabbitMQ'])
        );

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
