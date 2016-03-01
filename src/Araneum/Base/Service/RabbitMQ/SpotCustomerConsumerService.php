<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Araneum\Bundle\AgentBundle\AgentEvents;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Service;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Araneum\Bundle\AgentBundle\Event\CustomerEvent;

/**
 * Class SpotCustomerConsumerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class SpotCustomerConsumerService implements ConsumerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var SpotApiSenderService
     */
    private $spotApiSenderService;
    /**
     * @var MessageConversionHelper
     */
    private $msgConvertHelper;

    /**
     * Consumer constructor.
     *
     * @param SpotApiSenderService     $spotApiSenderService
     * @param MessageConversionHelper  $msgConvertHelper
     * @param EntityManager            $em
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        SpotApiSenderService $spotApiSenderService,
        MessageConversionHelper $msgConvertHelper,
        EntityManager $em,
        EventDispatcherInterface $dispatcher
    ) {
        $this->spotApiSenderService = $spotApiSenderService;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
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
        $log = (array) $data->log;
        try {
            $spotResponse = $this->spotApiSenderService->send((array) $data->data, (array) $data->spotCredential);
            if ($this->spotApiSenderService->getErrors($spotResponse) !== null) {
                throw new RequestException($this->spotApiSenderService->getErrors($spotResponse));
            }

            $this->updateCustomer($log);
            if ($log['action'] == CustomerLog::ACTION_CREATE) {
                /** @var Customer $customer */
                $customer = $this->em->getRepository("AraneumAgentBundle:Customer")->findOneById($log['customerId']);
                $customer->setPassword($data->data->password);
                $this->createCustomerEvent($customer, AgentEvents::CUSTOMER_LOGIN);
            }

            $this->createCustomerLog($log, $spotResponse->getBody(true), CustomerLog::STATUS_OK);
        } catch (RequestException $e) {
            $this->createCustomerLog($log, $e->getMessage(), CustomerLog::STATUS_ERROR);
        }
    }

    /**
     * Create and save customer log
     *
     * @param array  $log
     * @param string $logMessage
     * @param int    $status
     * @throws \Doctrine\ORM\ORMException
     */
    private function createCustomerLog(array $log, $logMessage, $status)
    {
        $customerLog = (new CustomerLog())
            ->setAction($log['action'])
            ->setApplication($this->em->getReference('AraneumMainBundle:Application', $log['applicationId']))
            ->setCustomer($this->em->getReference('AraneumAgentBundle:Customer', $log['customerId']))
            ->setResponse($logMessage)
            ->setStatus($status);

        $this->em->persist($customerLog);
        $this->em->flush();
    }

    /**
     * Update customer $deliveredAt
     *
     * @param array $log
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateCustomer(array $log)
    {
        if ($log['action'] == CustomerLog::ACTION_CREATE) {
            $customer = $this->em->getRepository("AraneumAgentBundle:Customer")->findOneById($log['customerId']);
            $customer->setDeliveredAt(new \DateTime());
            $this->em->persist($customer);
            $this->em->flush();
        }
    }

    /**
     * Create and dispatch Customer event
     *
     * @param Customer $customer
     * @param string   $eventName
     */
    private function createCustomerEvent(Customer $customer, $eventName)
    {
        $event = new CustomerEvent();
        $event->setCustomer($customer);
        $this->dispatcher->dispatch($eventName, $event);
    }
}
