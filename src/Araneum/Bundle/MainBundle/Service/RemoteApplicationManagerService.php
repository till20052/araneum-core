<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\Problem;
use Araneum\Bundle\AgentBundle\Service\AgentLoggerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Service\Client;
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
        'setSpotUserData' => [
            'method' => 'POST',
            'uri' => '/api/user/%d/spotUserData',
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
     * @var $guzzleOptions
     */
    private $guzzleOptions;

    /**
     * Remote application handler constructor
     *
     * @param Client        $client
     * @param EntityManager $entityManager
     * @param array         $siteApi
     */
    public function __construct(Client $client, EntityManager $entityManager, array $siteApi)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->guzzleOptions = [
            'auth' => [
                $siteApi['user'],
                $siteApi['password'],
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
            $this->guzzleOptions,
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
            $this->guzzleOptions,
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
     * @return string
     */
    public function sendRequest($host, $uri, $header, $body, $params, $method, Application $application = null)
    {
        $code = null;
        try {
            $host = substr($host, 0, 4) === "http" ? $host : 'http://'.$host;
            $response = $this
                ->client
                ->createRequest($method, $host.$uri, $header, $body, $params)
                ->send();
            $code = $response->getStatusCode();
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $code = $e->getCode();
        } catch (CurlException $e) {
            $response = new GuzzleResponse($e->getCode());
            $response->setBody($e->getError());
            $response->setStatus($e->getError());
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
     * Send spot User data to site by API
     *
     * @param Customer $customer
     * @param array    $spotData
     * @return string
     */
    public function setSpotUserData(Customer $customer, array $spotData)
    {
        $schema = 'http'.($customer->getApplication()->isUseSsl() ? 's' : '').'://';
        $host = $schema.$customer->getApplication()->getDomain();
        $response = $this->sendRequest(
            $host,
            sprintf(self::$requestParams['setSpotUserData']['uri'], $customer->getSiteId()),
            [],
            [
                'spotUserId' => $spotData['customerId'],
                'spotUserSession' => $spotData['spotsession'],
            ],
            $this->guzzleOptions,
            self::$requestParams['setSpotUserData']['method']
        );

        return $response;
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
            $this->guzzleOptions,
            $method,
            $application
        );

        return $response;
    }
}
