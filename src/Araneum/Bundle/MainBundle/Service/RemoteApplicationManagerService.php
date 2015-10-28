<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\UserBundle\DataFixtures\ORM\UserData;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Message\Request;
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
     *
     * @param int $clusterId
     * @return mixed
     */
    public function get($clusterId)
    {

        $repository = $this->entityManager->getRepository('AraneumMainBundle:Cluster');
        $connections = $repository->find($clusterId)->getHosts()->getValues();
        $connection = reset($connections);

		$response = $this->sendRequest($connection, '/api/cluster/application/list', null, 'GET');
    }

    /**
     * Create application on cluster
     *
     * @param string $appKey
     * @return bool
     */
    public function create($appKey)
    {
		$repository = $this->entityManager->getRepository('AraneumMainBundle:Application');
		$application = $repository->findOneBy(['appKey' => $appKey]);
		$connections = $application->getCluster()->getHosts()->getValues();
		$connection = reset($connections);

		$locales = $application->getLocales();

		$i = 1;
		$countLocales = count($locales);
		$paramLocale = '';

		foreach ($locales as $locale) {
			$paramLocale = $locale->getLocale();
			if ($i < $countLocales) {
				$paramLocale .= '|';
			}
		}

		$params = [
			'auth' => [
				UserData::API_USER,
				UserData::API_PASSWD
			],
			'query' => [
				'domain' => $application->getDomain(),
				'template' => $application->getTemplate(),
				'cluster' => $application->getCluster(),
				'db_name' => $application->getDb()->getName(),
				'db_host' => $application->getDb()->getHost(),
				'db_port' => $application->getDb()->getPort(),
				'db_user_name' => $application->getDb()->getUserName(),
				'db_password' => $application->getDb()->getPassword(),
				'locale' => $paramLocale
			]
		];

		$response = $this->sendRequest($connection - getHost(), '', $params, 'POST');
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
        $repository = $this->entityManager->getRepository('AraneumMainBundle:Application');
        $connections = $repository->findOneBy(['appKey' => $appKey])->getCluster()->getHosts()->getValues();
        $connection = reset($connections);
	}

	public function sendRequest($connection, $uri, $params, $method)
	{
		try {

			$response = $this
				->client
				->createRequest($method, 'http://' . $connection->getHost() . $uri, $params)
				->send();

			$status = in_array(
				$response->getStatusCode(),
				range(Response::HTTP_OK, Response::HTTP_MULTI_STATUS) + [Response::HTTP_IM_USED]
			);
		} catch (CurlException $e) {
			return $e;
		}

		return $response;
	}
}