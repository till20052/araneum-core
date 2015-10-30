<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Service\ApplicationApiHandlerService;
use Araneum\Bundle\MainBundle\Service\RemoteApplicationManagerService;
use Doctrine\Common\Collections\ArrayCollection;

class RemoteApplicationManagerTest extends BaseController
{
    protected $manager;

    //protected $application;

    protected $connection;

    protected $client;

    protected $request;

    protected $response;

    protected $connectionRepository;


    /**
     * Method that called before tests.
     *
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connection = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Connection');

        $this->connection->expects($this->once())
            ->method('getHost')
            ->will($this->returnValue('127.0.0.1'));

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Connection'))
            ->will($this->returnValue($this->connectionRepository));


        $this->request = $this->getMockBuilder('Guzzle\Http\Message\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Guzzle\Http\Message\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->response));

        $this->client = $this
            ->getMockBuilder('Guzzle\Service\Client')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test ApplicationApiHandlerService verifies that returns an array of the desired keys and values
     */
    public function testGet()
    {
        $remoteApplicationManager = new RemoteApplicationManagerService($this->manager, $this->client);

        $this->connectionRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(['clusters' => 123]))
            ->will($this->returnValue([$this->connection]));


        $this->client->expects($this->once())
            ->method('createRequest')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo('http://127.0.0.1/api/cluster/application/list'),
                $this->equalTo(null),
                $this->equalTo(null),
                $this->equalTo([])
            )
            ->will($this->returnValue($this->request));

        $appConfig = $remoteApplicationManager->get(123);

        $this->assertEquals(200, $appConfig);
    }

    public function testCreateOrUpdatePrepare()
    {
        $remoteApplicationManager = new RemoteApplicationManagerService($this->manager, $this->client);

        $application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');

        $locale = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');

        $cluster = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Cluster');

        $locale->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en_US'));

        $applicationRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Connection'))
            ->will($this->returnValue($applicationRepository));

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

        $application->expects($this->once())
            ->method('getDb')
            ->will($this->returnValue($this->connection));

        $this->connection->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('Db_name'));

        $this->connection->expects($this->once())
            ->method('getHost')
            ->will($this->returnValue('127.0.0.1'));

        $this->connection->expects($this->once())
            ->method('getHost')
            ->will($this->returnValue('127.0.0.1'));

        $this->connection->expects($this->once())
            ->method('getPort')
            ->will($this->returnValue(5432));

        $this->connection->expects($this->once())
            ->method('getUserName')
            ->will($this->returnValue('postrgese'));

        $this->connection->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue('password'));

        $appConfig = $remoteApplicationManager->get(123);
    }
}