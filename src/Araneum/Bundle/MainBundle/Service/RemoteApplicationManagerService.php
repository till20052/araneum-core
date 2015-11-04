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
use Symfony\Component\Yaml\Yaml;

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
     * @var $params
     */
    private $params;

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
        $configArray = Yaml::parse(__DIR__ . '/../../../../../app/config/config.yml');

        $user = $configArray['parameters']['site_api']['user'];
        $password = $configArray['parameters']['site_api']['password'];

        $this->params = [
            'auth' => [
                $user,
                $password
            ]
        ];
    }

    /**
     * Get application data from cluster
     *
     * @param int $clusterId
     * @return mixed
     */
    public function get($clusterId)
    {

        $repository = $this->entityManager->getRepository('AraneumMainBundle:Connection');
        $connections = $repository->getHostByClusterId($clusterId);
        $connection = reset($connections);

        $response = $this->sendRequest(
            $connection->getHost(),
            '/api/cluster/application/list',
            null,
            null,
            $this->params,
            'GET'
        );

        return $response;
    }

    /**
     * Create application on cluster
     *
     * @param string $appKey
     * @return bool
     */
    public function create($appKey)
    {
        $response = $this->createOrUpdatePreparation($appKey, 'POST', '/api/cluster/application/insert');

        return $response;
    }

    /**
     * Update application config in cluster
     *
     * @param string $appKey
     * @return bool
     */
    public function update($appKey)
    {
        $response = $this->createOrUpdatePreparation($appKey, 'PUT', '/api/cluster/application/update/');

        return $response;
    }

    /**
     * Remove application from cluster
     *
     * @param string $appKey
     * @return bool
     */
    public function remove($appKey)
    {
        $connections = $this->entityManager->getRepository('AraneumMainBundle:Connection')->findConnectionByAppKey(
            $appKey
        );

        $connection = reset($connections);

        $response = $this->sendRequest(
            $connection->getHost(),
            '/api/cluster/application/delete/' . $appKey,
            null,
            null,
            $this->params,
            'DELETE'
        );

        return $response;
    }

    /**
     * Request
     *
     * @param $host
     * @param $uri
     * @param $header
     * @param $body
     * @param $params
     * @param $method
     * @return \Exception|CurlException|GuzzleResponse
     */
    public function sendRequest($host, $uri, $header, $body, $params, $method)
    {
        try {
            $response = $this
                ->client
                ->createRequest($method, 'http://' . $host . $uri, $header, $body, $params)
                ->send();
        } catch (CurlException $e) {
            return false;
        }

        return $response;
    }

    /**
     * Prepare request for insert and update
     *
     * @param $appKey
     * @param $method
     * @param $uri
     * @return \Exception|CurlException|GuzzleResponse
     */
    private function createOrUpdatePreparation($appKey, $method, $uri)
    {
        $application = $this->entityManager->getRepository('AraneumMainBundle:Application')->findOneBy(
            ['appKey' => $appKey]
        );
        $connections = $this->entityManager->getRepository('AraneumMainBundle:Connection')->findConnectionByAppKey(
            $appKey
        );
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

        $db = [
            'name' => $application->getDb()->getName(),
            'host' => $application->getDb()->getHost(),
            'port' => $application->getDb()->getPort(),
            'user_name' => $application->getDb()->getUserName(),
            'password' => $application->getDb()->getPassword(),
        ];

        $clusterId = ['id' => $application->getCluster()->getId()];

        $request = [
            'domain' => $application->getDomain(),
            'template' => $application->getTemplate(),
            'cluster' => $clusterId,
            'locales' => $paramLocale,
            'components' => '123',
            //temporary not used but must be not empty
            'app_key' => $application->getAppKey(),
            'db' => $db
        ];

        if ($method == 'PUT') {
            $uri .= $application->getDomain();
        }

        $response = $this->sendRequest($connection->getHost(), $uri, null, $request, $this->params, $method);

        return $response;
    }

}