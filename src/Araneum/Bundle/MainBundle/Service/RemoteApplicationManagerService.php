<?php

namespace Araneum\Bundle\MainBundle\Service;

use Doctrine\ORM\EntityManager;
use Guzzle\Service\Client;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Message\Response as GuzzleResponse;

class RemoteApplicationManagerService
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Client
     */
    private $client;

    /**
     * Remote application handler constructor
     *
     * @param EntityManager $entityManager
     * @param Client        $client
     */
    public function __construct(EntityManager $entityManager, Client $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * Get application data from cluster
     */
    public function get($clusterId)
    {
        $repository = $this->entityManager->getRepository('AraneumMainBundle:Application');
        $connections = $repository->findByConnectionId($clusterId);

        try {
            $response = $this->client
                ->get('http://' . $connections->getDomain())
                ->send();

            $status = in_array(
                $response->getStatusCode(),
                range(Response::HTTP_OK, Response::HTTP_MULTI_STATUS) + [Response::HTTP_IM_USED]
            );
        } catch (CurlException $e) {
            return $e;
        }
    }

    /**
     * Create application on cluster
     *
     * @param string $appKey
     * @return bool
     */
    public function create($appKey)
    {
        return true;
    }

    /**
     * Update application config in cluster
     *
     * @param string $appKey
     * @return bool
     */
    public function update($appKey)
    {
        return true;
    }

    /**
     * Remove application from cluster
     *
     * @param string $appKey
     * @return bool
     */
    public function remove($appKey)
    {
        return true;
    }
}