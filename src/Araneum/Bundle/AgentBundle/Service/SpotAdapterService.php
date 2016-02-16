<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Base\Service\Spot\SpotApiSenderService;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Symfony\Component\Security\Acl\Exception\Exception;

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
     * SpotAdapterService constructor.
     *
     * @param EntityManager        $entityManager
     * @param SpotApiSenderService $spotApiSenderService
     */
    public function __construct(
        EntityManager $entityManager,
        SpotApiSenderService $spotApiSenderService
    ) {
        $this->spotApiSenderService = $spotApiSenderService;
        $this->em  = $entityManager;
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
        $data = json_decode($postData['data']);
        $application = $this->em->getRepository('AraneumMainBundle:Application')->findOneByAppKey($appKey);
        if (!$application) {

            throw new Exception('Application with key '.$appKey.' doesn\'n exist');
        }
        $spotCredential = $application->getSpotCredential();
        $response = $this->spotApiSenderService->send($data, $spotCredential);
        if ($this->spotApiSenderService->getErrors($response) !== null) {
            throw new RequestException($this->spotApiSenderService->getErrors($response));
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
        if (!isset($data['appKey']) || empty($data['appKey'])) {
            $errors['appKey'] = 'appKey should exist and should be valid as parameter of request';
        }
        $spotData = $data = json_decode($data['data']);;
        if (!isset($spotData['COMMAND']) || empty($spotData['COMMAND'])) {
            $errors['command'] = 'COMMAND should be required and valid to send spot request';
        }
        if (!isset($spotData['MODULE']) || empty($spotData['MODULE'])) {
            $errors['MODULE'] = 'MODULE should be required and valid to send spot request';
        }
        if (!empty ($errors)) {
            throw new RequestException(json_encode($errors));
        }
    }
}
