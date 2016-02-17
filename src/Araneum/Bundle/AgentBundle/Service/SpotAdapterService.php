<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Base\Service\Spot\SpotApiSenderService;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Symfony\Component\Security\Acl\Exception\Exception;
use Araneum\Base\Service\RabbitMQ\SpotProducerService;

/**
 * Class SpotAdapterService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotAdapterService
{
    /**
     * @var SpotApiSenderService
     */
    protected $spotApiSenderService;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SpotProducerService
     */
    protected $spotProducerService;

    /**
     * SpotAdapterService constructor.
     *
     * @param EntityManager        $entityManager
     * @param SpotApiSenderService $spotApiSenderService
     * @param SpotProducerService  $spotProducerService
     */
    public function __construct(
        EntityManager $entityManager,
        SpotApiSenderService $spotApiSenderService,
        SpotProducerService  $spotProducerService
    ) {
        $this->spotApiSenderService = $spotApiSenderService;
        $this->em  = $entityManager;
        $this->spotProducerService = $spotProducerService;
    }

    /**
     * SpotAdapterService constructor.
     *
     * @param array $postData
     *
     * @return mixed
     */
    public function sendRequestToSpot($postData)
    {
        $this->validateInputData($postData);
        $appKey = $postData['appKey'];
        $data = array_merge(
            [
                'COMMAND' => $postData['COMMAND'],
                'MODULE' => $postData['MODULE'],
            ],
            json_decode($postData['requestData'])
        );

        $application = $this->em->getRepository('AraneumMainBundle:Application')->findOneByAppKey($appKey);
        if (!$application) {
            throw new Exception('Application with key '.$appKey.' doesn\'n exist');
        }
        $spotCredential = $application->getSpotCredential();
        $response = true;
        if (!$postData['guaranteeDelivery']) {
            $response = $this->spotApiSenderService->send($data, $spotCredential);
            if ($this->spotApiSenderService->getErrors($response) !== null) {
                throw new RequestException($this->spotApiSenderService->getErrors($response));
            }
        } else {
            $this->spotProducerService->publish($data, $spotCredential);
        }

        return $response;
    }

    /**
     * SpotAdapterService validate data method.
     *
     * @param array $data
     * @return mixed
     */
    protected function validateInputData($data)
    {
        $errors = [];
        if (!isset($data['requestData']) || empty($data['requestData'])) {
            $errors['appKey'] = 'appKey should exist and should be valid as parameter of request';
        }
        if (!isset($data['COMMAND']) || empty($data['COMMAND'])) {
            $errors['COMMAND'] = 'COMMAND should be required and valid to send spot request';
        }
        if (!isset($data['MODULE']) || empty($data['MODULE'])) {
            $errors['MODULE'] = 'MODULE should be required and valid to send spot request';
        }
        if (isset($data['requestData']) && !empty($data['requestData'])) {
            $errors['requestData'] = 'requestData should be required and valid to send spot request';
        }
        if (!empty ($errors)) {
            throw new RequestException(json_encode($errors));
        }
        if (!isset($data['guaranteeDelivery'])) {
            $data['guaranteeDelivery'] = false;
        }
    }
}
