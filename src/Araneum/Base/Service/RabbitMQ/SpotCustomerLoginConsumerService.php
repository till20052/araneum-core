<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\MainBundle\Service\RemoteApplicationManagerService;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Service;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SpotCustomerLoginConsumerService
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class SpotCustomerLoginConsumerService implements ConsumerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var string
     */
    protected $spotApiPublicUrlLogin;
    /**
     * @var RemoteApplicationManagerService
     */
    protected $remoteApplicationManager;
    /**
     * @var SerializerInterface
     */
    protected $serializer;
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
     * @param SpotApiSenderService            $spotApiSenderService
     * @param MessageConversionHelper         $msgConvertHelper
     * @param EntityManager                   $em
     * @param SerializerInterface             $serializer
     * @param RemoteApplicationManagerService $remoteApplicationManager
     * @param string                          $spotApiPublicUrlLogin
     */
    public function __construct(
        SpotApiSenderService $spotApiSenderService,
        MessageConversionHelper $msgConvertHelper,
        EntityManager $em,
        SerializerInterface $serializer,
        RemoteApplicationManagerService $remoteApplicationManager,
        $spotApiPublicUrlLogin
    ) {
        $this->spotApiSenderService = $spotApiSenderService;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->em = $em;
        $this->serializer = $serializer;
        $this->remoteApplicationManager = $remoteApplicationManager;
        $this->spotApiPublicUrlLogin = $spotApiPublicUrlLogin;
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
        /** @var Customer $customer */
        $customer = $this->serializer->deserialize($data->data, 'Araneum\Bundle\AgentBundle\Entity\Customer', 'json');
        try {
            $spotResponse = $this->spotApiSenderService->sendToPublicUrl(
                Request::METHOD_POST,
                $customer->getApplication()->getSpotApiPublicUrl(),
                $this->spotApiPublicUrlLogin,
                [
                    'email' => $customer->getEmail(),
                    'password' => $customer->getPassword(),
                ]
            );

            if ($this->spotApiSenderService->getErrorsFromPublic($spotResponse) !== null) {
                throw new RequestException($this->spotApiSenderService->getErrorsFromPublic($spotResponse));
            }

            $decodedResponse = $spotResponse->json();
            $spotCustomerData = [
                'spotsession' => $this->spotApiSenderService->getSpotSessionFromPublic($spotResponse->getSetCookie()),
                'customerId' => $decodedResponse['customerId'],
            ];
            $this->remoteApplicationManager->setSpotUserData($customer, $spotCustomerData);

            $this->createCustomerLog($customer, $spotResponse->getBody(true), CustomerLog::STATUS_OK);
        } catch (RequestException $e) {
            $this->createCustomerLog($customer, $e->getMessage(), CustomerLog::STATUS_ERROR);
        }
    }

    /**
     * Create and save customer log
     *
     * @param Customer $customer
     * @param string   $logMessage
     * @param int      $status
     * @throws \Doctrine\ORM\ORMException
     */
    private function createCustomerLog(Customer $customer, $logMessage, $status)
    {
        $customerLog = (new CustomerLog())
            ->setAction(CustomerLog::ACTION_LOGIN)
            ->setApplication($this->em->getReference('AraneumMainBundle:Application', $customer->getApplication()->getId()))
            ->setCustomer($this->em->getReference('AraneumAgentBundle:Customer', $customer->getId()))
            ->setResponse($logMessage)
            ->setStatus($status);

        $this->em->persist($customerLog);
        $this->em->flush();
    }
}
