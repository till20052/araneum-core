<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\AgentBundle\Entity\Problem;
use Araneum\Bundle\AgentBundle\Service\AgentLoggerService;
use Araneum\Bundle\UserBundle\DataFixtures\ORM\UserData;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\Request;
use Guzzle\Service\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Symfony\Component\Yaml\Yaml;
use Araneum\Bundle\MainBundle\Entity\Application;

/**
 * Class RemoteApplicationManagerService
 *
 * @package Araneum\Bundle\MainBundle\Service
 */
class RemoteApplicationManagerService
{
    /**
     * @var array
     */
    private static $requestParams = [
        'get' => [
            'method' => 'GET',
            'uri' => '/api/cluster/application/list',
        ],
        'create' => [
            'method' => 'POST',
            'uri' => '/api/cluster/application/insert',
        ],
        'update' => [
            'method' => 'PUT',
            'uri' => '/api/cluster/application/update/',
        ],
        'remove' => [
            'method' => 'DELETE',
            'uri' => '/api/cluster/application/delete/',
        ],
    ];

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
     * @var $container
     */
    private $container;

    /**
     * @var
     */
    private $apiCred;

    /**
     * Remote application handler constructor
     *
     * @param Client             $client
     * @param ContainerInterface $container
     */
    public function __construct(Client $client, ContainerInterface $container)
    {
        $this->container = $container;
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        $this->client = $client;

        $this->apiCred = $this->container->getParameter('site_api');
        $user = $this->apiCred['user'];
        $password = $this->apiCred['password'];

        $this->params = [
            'auth' => [
                $user,
                $password,
            ],
            'connect_timeout' => 1,
        ];
    }

    /**
     * Get application data from cluster
     *
     * @param  int $clusterId
     * @return mixed
     */
    public function get($clusterId)
    {

        $repository = $this->entityManager->getRepository('AraneumMainBundle:Connection');
        $connections = $repository->getHostByClusterId($clusterId);
        $connection = reset($connections);

        $response = $this->sendRequest(
            $connection->getHost(),
            self::$requestParams['get']['uri'],
            null,
            null,
            $this->params,
            self::$requestParams['get']['method']
        );

        return $response;
    }

    /**
     * Create application on cluster
     *
     * @param  string $appKey
     * @return bool
     */
    public function create($appKey)
    {
        $response = $this->createOrUpdatePreparation(
            $appKey,
            self::$requestParams['create']['method'],
            self::$requestParams['create']['uri']
        );

        return $response;
    }

    /**
     * Update application config in cluster
     *
     * @param  string $appKey
     * @return bool
     */
    public function update($appKey)
    {
        $response = $this->createOrUpdatePreparation(
            $appKey,
            self::$requestParams['update']['method'],
            self::$requestParams['update']['uri']
        );

        return $response;
    }

    /**
     * Remove application from cluster
     *
     * @param  string $appKey
     * @return bool
     */
    public function remove($appKey)
    {
        $connections = $this->entityManager
            ->getRepository('AraneumMainBundle:Connection')
            ->findConnectionByAppKey($appKey);

        $connection = reset($connections);

        $response = $this->sendRequest(
            $connection->getHost(),
            self::$requestParams['remove']['uri'].$appKey,
            null,
            null,
            $this->params,
            self::$requestParams['remove']['method']
        );

        return $response;
    }

    /**
     * Request
     *
     * @param  string                          $host
     * @param  string                          $uri
     * @param  array|\Guzzle\Common\Collection $header
     * @param  array|string                    $body
     * @param  array                           $params
     * @param  string                          $method
     * @param  Application                     $application
     * @return \Exception|CurlException|GuzzleResponse
     */
    public function sendRequest($host, $uri, $header, $body, $params, $method, Application $application = null)
    {
        $code = null;

        try {
            $response = $this
                ->client
                ->createRequest($method, 'http://'.$host.$uri, $header, $body, $params)
                ->send();
            $code = $response->getStatusCode();
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $code = $e->getCode();
        } catch (CurlException $e) {
            $response = new GuzzleResponse($e->getCode());
            $response->setBody($e->getCurlHandle()->getError());
            $response->setStatus($e->getCurlHandle()->getError());
            $code = $response->getStatusCode();
        } catch (\Exception $e) {
            $response = new GuzzleResponse($e->getCode());
            $response->setBody($e->getMessage());
            $code = $response->getStatusCode();
        }

        if (!is_null($application)) {

            $application->setStatus($response->isSuccessful() ? 0 : 100);

            $problem = new Problem();
            $problem->setStatus($code);
            $problem->setDescription($response->getMessage());

            $logApplication = new AgentLoggerService($this->entityManager);
            $logApplication->logApplication($application, $code, new ArrayCollection([$problem]));
        }

        return $response->getBody(true);
    }

    /**
     * Prepare request for insert and update
     *
     * @param  $appKey
     * @param  $method
     * @param  $uri
     * @return \Exception|CurlException|GuzzleResponse
     */
    private function createOrUpdatePreparation($appKey, $method, $uri)
    {
        $application = $this->entityManager
            ->getRepository('AraneumMainBundle:Application')
            ->findOneBy(['appKey' => $appKey]);

        $connections = $this->entityManager
            ->getRepository('AraneumMainBundle:Connection')
            ->findConnectionByAppKey($appKey);

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
            'application_name' => $application->getName(),
            'domain' => $application->getDomain(),
            'template' => $application->getTemplate(),
            'cluster' => $clusterId,
            'locales' => $paramLocale,
            //temporary not used but must be not empty
            'components' => '123',
            'app_key' => $application->getAppKey(),
            'db' => $db,
        ];

        if ($method == 'PUT') {
            $uri .= $application->getDomain();
        }

        $response = $this->sendRequest(
            $connection->getHost(),
            $uri,
            null,
            $request,
            $this->params,
            $method,
            $application
        );

        return $response;
    }
}
