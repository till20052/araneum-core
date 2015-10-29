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

    protected $application;

    protected $connection;

    protected $client;

    protected $request;

    protected $response;



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

        $entityRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $connectionRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connection = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Connection');

        $this->application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');

        $this->connection->expects($this->once())
            ->method('getHost')
            ->will($this->returnValue('127.0.0.1'));

        $this->manager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Connection'))
            ->will($this->returnValue($connectionRepository));

        $connectionRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(['clusters' => 123]))
            ->will($this->returnValue([$this->connection]));

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
        
    }
}