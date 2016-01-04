<?php

namespace Araneum\Base\Service\RabbitMQ;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Service;
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
     * @var SpotApiSenderService
     */
    private $spotApiSenderService;
    /**
     * @var
     */
    private $msgConvertHelper;

    /**
     * Consumer constructor.
     *
     * @param SpotApiSenderService    $spotApiSenderService
     * @param MessageConversionHelper $msgConvertHelper
     * @param EntityManager           $em
     */
    public function __construct(
        SpotApiSenderService $spotApiSenderService,
        MessageConversionHelper $msgConvertHelper,
        EntityManager $em,
        $spotApiPublicUrlLogin
    ) {
        $this->spotApiSenderService = $spotApiSenderService;
        $this->msgConvertHelper = $msgConvertHelper;
        $this->em = $em;
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
        $log = (array) $data->log;
        $spoPublicUrl = $data->spoPublictUrl;

        try {
            $spotResponse = $this->spotApiSenderService->sendToPublicUrl(
                Request::POST,
                $spoPublicUrl,
                $this->spotApiPublicUrlLogin,
                (array) $data->data
            );

            if ($this->spotApiSenderService->getErrorsFromPublic($spotResponse) !== null) {
                throw new RequestException($this->spotApiSenderService->getErrors($spotResponse));
            }

            $decodedResponse = $spotResponse->json();

            // TODO send set spotsession
            $spotCustomerData = [
                'spotsession' => $this->spotApiSenderService->getSpotSessionFromPublic($spotResponse->getSetCookie()),
                'customerId' => $decodedResponse['customerId'],
            ];

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
            ->setSpotResponse($logMessage)
            ->setStatus($status);

        $this->em->persist($customerLog);
        $this->em->flush();
    }
}