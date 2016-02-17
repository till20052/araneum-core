<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Service;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Araneum\Bundle\AgentBundle\Entity\Customer;

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
     * @var SpotOptionService
     */
    protected $spotOptionService;
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
     * @param SpotApiSenderService $spotApiSenderService
     * @param MessageConversionHelper $msgConvertHelper
     * @param EntityManager $em
     * @param SpotOptionService $spotOptionService
     */
    public function __construct(
        SpotApiSenderService $spotApiSenderService,
        MessageConversionHelper $msgConvertHelper,
        EntityManager $em,
        SpotOptionService $spotOptionService
    )
    {
        $this->spotApiSenderService = $spotApiSenderService;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->em = $em;
        $this->spotOptionService = $spotOptionService;
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
        $log = (array)$data->log;
        try {
            $spotResponse = $this->spotApiSenderService->send((array)$data->data, (array)$data->spotCredential);
            if ($this->spotApiSenderService->getErrors($spotResponse) !== null) {
                throw new RequestException($this->spotApiSenderService->getErrors($spotResponse));
            }
            $this->updateCustomer($log);
            $this->loginCustomerInSpot($data->data->password, $log);
            $this->createCustomerLog($log, $spotResponse->getBody(true), CustomerLog::STATUS_OK);
        } catch (RequestException $e) {
            $this->createCustomerLog($log, $e->getMessage(), CustomerLog::STATUS_ERROR);
        }
    }

    /**
     * Create and save customer log
     *
     * @param array $log
     * @param string $logMessage
     * @param int $status
     * @throws \Doctrine\ORM\ORMException
     */
    private function createCustomerLog(array $log, $logMessage, $status)
    {
        $customerLog = (new CustomerLog())
            ->setAction($log['action'])
            ->setApplication($this->em->getReference('AraneumMainBundle:Application', $log['applicationId']))
            ->setCustomer($this->em->getReference('AraneumAgentBundle:Customer', $log['customerId']))
            ->setSpotResponse($logMessage)
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
     * Login customer in spot on create
     *
     * @param string $password
     * @param array $log
     */
    private function loginCustomerInSpot($password, array $log)
    {
        if ($log['action'] == CustomerLog::ACTION_CREATE) {
            $customer = $this->em->getRepository("AraneumAgentBundle:Customer")->findOneById($log['customerId']);
            $customer->setPassword($password);
            $this->spotOptionService->login($customer);
        }
    }
}
