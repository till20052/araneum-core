<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
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
     * @param string $appKey
     * @param array  $postData
     *
     * @return mixed
     */
    public function sendRequestToSpot($appKey, $postData)
    {
        $application = $this->em->getRepository('AraneumMainBundle:Application')->findOneByAppKey($appKey);

        if (!$application) {
            throw new Exception('Application with key '.$appKey.' doesn\'n exist');
        }

        $spotCredential = $application->getSpotCredential();

        if (empty($postData['guaranteeDelivery'])) {

            $response = $this->spotApiSenderService->send($postData, $spotCredential);

            if ($this->spotApiSenderService->getErrors($response) !== null) {
                throw new RequestException($this->spotApiSenderService->getErrors($response));
            }

            return $response->getBody(true);
        } else {
            unset($postData['guaranteeDelivery']);

            return $this->spotProducerService->publish($postData, $spotCredential);
        }
    }
}
