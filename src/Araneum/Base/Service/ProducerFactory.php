<?php

namespace Araneum\Base\Service;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Araneum\Base\Service\RabbitMQ\MessageConversionHelper;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Araneum\Base\Service\RabbitMQ\ProducerService;

/**
 * Abstract class AbstractApiSender
 *
 * @package Araneum\Base\Service
 */
class ProducerFactory
{
    /**
     * @var Container
     */
    private $container;
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
     * @param Container               $container
     * @param MessageConversionHelper $msgConvertHelper
     * @param string                  $queueExpiration
     */
    public function __construct(Container $container, MessageConversionHelper $msgConvertHelper, $queueExpiration)
    {
        $this->container = $container;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->queueExpiration = $queueExpiration;
        $this->getConfigOptions();
    }

    /**
     * Gets config options to create a service.
     */
    public function getConfigOptions()
    {
        $this->container->setParameter('fucking_parametr', 'hI fucking parametr');
    }

    /**
     * Create producer service method
     */
    public function createProducer()
    {
        return new ProducerService();
    }
}
