<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Service\RemoteApplicationManagerService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RemoteApplicationManagerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service
 */
class RemoteApplicationManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    protected $connection;

    protected $client;

    protected $request;

    protected $response;

    protected $connectionRepository;

    protected $user;

    protected $password;

    /**
     * @var RemoteApplicationManagerService
     */
    protected $remoteApplicationManager;

    /**
     * Method that called before tests.
     *
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->user = 'apiUser';
        $this->password = 'apiPassword';

        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionRepository = $this
            ->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ConnectionRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connection = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Connection');

        $this->connection->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('127.0.0.1'));

        $this->response = $this->getMockBuilder('\Guzzle\Http\Message\Response')
            ->setConstructorArgs([Response::HTTP_OK])
            ->getMock();
        $this->response->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $this->request = $this->getMockBuilder('\Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->response));

        $this->client = $this
            ->getMockBuilder('\Guzzle\Service\Client')
            ->setConstructorArgs([Response::HTTP_OK])
            ->getMock();

        $this->remoteApplicationManager = new RemoteApplicationManagerService(
            $this->client,
            $this->manager,
            [
                'user' => $this->user,
                'password' => $this->password,
            ]
        );
    }

    /**
     * Test ApplicationApiHandlerService verifies that r an array of the desired keys and values.
     */
    public function testGet()
    {
        $this->connectionRepository->expects($this->once())
            ->method('getHostByClusterId')
            ->with($this->equalTo(123))
            ->will($this->returnValue([$this->connection]));

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Connection'))
            ->will($this->returnValue($this->connectionRepository));

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo('http://127.0.0.1/api/cluster/application/list'),
                $this->equalTo(null),
                $this->equalTo(null),
                $this->equalTo(
                    [
                        'auth' => [
                            $this->user,
                            $this->password,
                        ],
                        'connect_timeout' => 1,
                    ]
                )
            )
            ->will($this->returnValue($this->request));

        $appConfig = $this->remoteApplicationManager->get(123);

        $this->assertEquals(null, $appConfig);
    }

    /**
     *  Test Create or Update method
     */
    public function testCreateorUpdate()
    {
        $application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');

        $locale = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Locale');

        $cluster = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Cluster');

        $locale->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en_US'));

        $applicationRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager->expects($this->at(0))
            ->method('getRepository')
            ->with('AraneumMainBundle:Application')
            ->will($this->returnValue($applicationRepository));

        $this->manager->expects($this->at(1))
            ->method('getRepository')
            ->with('AraneumMainBundle:Connection')
            ->will($this->returnValue($this->connectionRepository));

        $applicationRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(['appKey' => 123]))
            ->will($this->returnValue($application));

        $this->connectionRepository->expects($this->once())
            ->method('findConnectionByAppKey')
            ->with($this->equalTo(123))
            ->will($this->returnValue([$this->connection]));

        $application->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue([$locale]));

        $application->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('testName'));

        $application->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('domain'));

        $application->expects($this->once())
            ->method('getTemplate')
            ->will($this->returnValue('defaultTemplate'));

        $application->expects($this->once())
            ->method('getCluster')
            ->will($this->returnValue($cluster));

        $application->expects($this->once())
            ->method('getAppKey')
            ->will($this->returnValue(123));

        $application->expects($this->any())
            ->method('getDb')
            ->will($this->returnValue($this->connection));

        $this->connection->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('Db_name'));

        $this->connection->expects($this->once())
            ->method('getPort')
            ->will($this->returnValue(5432));

        $this->connection->expects($this->once())
            ->method('getUserName')
            ->will($this->returnValue('postrgese'));

        $this->connection->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue('password'));

        $params = [
            'auth' => [
                $this->user,
                $this->password,
            ],
            'connect_timeout' => 1,
        ];

        $query = [
            'application_name' => 'testName',
            'domain' => 'domain',
            'template' => 'defaultTemplate',
            'cluster' => ['id' => ''],
            'locales' => 'en_US',
            'components' => '123',
            'app_key' => '123',
            'db' => [
                'name' => 'Db_name',
                'host' => '127.0.0.1',
                'port' => 5432,
                'user_name' => 'postrgese',
                'password' => 'password',
            ],
        ];

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo('http://127.0.0.1/api/cluster/application/insert'),
                $this->equalTo(null),
                $this->equalTo($query),
                $this->equalTo($params)
            )
            ->will($this->returnValue($this->request));

        $appConfig = $this->remoteApplicationManager->create(123);

        $this->assertEquals(null, $appConfig);
    }

    /**
     * Test setSpotUserData
     */
    public function testSetSpotUserData()
    {
        $application = (new Application())
            ->setUseSsl(true)
            ->setDomain('test.com');
        $customer = (new Customer())
            ->setSiteId(1321)
            ->setApplication($application);
        $spotData = [
            'customerId' => 3333,
            'spotsession' => 'spotsession3333',
        ];
        $params = [
            'auth' => [
                $this->user,
                $this->password,
            ],
            'connect_timeout' => 1,
        ];

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo('https://test.com/api/user/1321/spotUserData'),
                $this->equalTo([]),
                $this->equalTo(
                    [
                        'spotUserId' => $spotData['customerId'],
                        'spotUserSession' => $spotData['spotsession'],
                    ]
                ),
                $this->equalTo($params)
            )
            ->will($this->returnValue($this->request));

        $this->remoteApplicationManager->setSpotUserData($customer, $spotData);
    }
}
