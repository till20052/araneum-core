<?php

namespace Araneum\Bundle\MainBundle\RabbitMQ\Callback;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class Service implements ConsumerInterface
{
    /**
     * @param AMQPMessage $msg
     * @return mixed
     */
    public function execute(AMQPMessage $msg)
    {
        var_dump(unserialize($msg->body));
    }
}