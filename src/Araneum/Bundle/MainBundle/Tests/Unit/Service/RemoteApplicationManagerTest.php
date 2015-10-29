<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Service\ApplicationApiHandlerService;
use Araneum\Bundle\MainBundle\Service\RemoteApplicationManagerService;
use Doctrine\Common\Collections\ArrayCollection;

class RemoteApplicationManagerTest extends BaseController
{
    protected $manager;

    protected $applicationRepository;

    protected $clusterRepository;

    protected $client;

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

        $this->applicationRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->clusterRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($this->applicationRepository));

        $this->manager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Cluster'))
            ->will($this->returnValue($this->clusterRepository));

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

        $connection = new Connection();
        $connection->setId(123);
        $connection->setName('hostConnection');
        $connection->setHost('127.0.0.1');

        $cluster = $this->getMock('Araneum\Bundle\MainBundle\Entity\Cluster');
        $cluster->expects($this->once())
            ->method('getHosts')
            ->will($this->returnValue([$connection]));

        $db = $this->getMock('Araneum\Bundle\MainBundle\Entity\Connection');

        $application = new Application();
        $application->setId(123);
        $application->setName('testname');
        $application->setType('testtype');
        $application->setAliases('www.testname.test, www2.testname.test');
        $application->setCluster($cluster);
        $application->setDb($db);
        $application->setDomain('testname.test');
        $application->setPublic();
        $application->setEnabled();
        $application->setLocales(new ArrayCollection([$locale]));
        $application->setComponents(new ArrayCollection([$component]));
        $application->setOwner($owner);
        $application->setStatus(1);
        $application->setTemplate('testtemplate');

        $testAppConfig = [
            'id' => 123,
            'name' => 'testname',
            'type' => 'testtype',
            'aliases' => 'www.testname.test, www2.testname.test',
            'cluster' => $cluster,
            'db' => $db,
            'domain' => 'testname.test',
            'public' => true,
            'enabled' => true,
            'locales' => new ArrayCollection([$locale]),
            'components' => new ArrayCollection([$component]),
            'owner' => $owner,
            'status' => 1,
            'template' => 'testtemplate'
        ];

        $this->repository
            ->expects($this->once())
            ->method("findOneBy")
            ->with($this->equalTo(['appKey' => self::API_KEY]))
            ->will($this->returnValue($application));
        $appConfig = $applicationHandler->get(self::API_KEY);

        $this->assertEquals($testAppConfig, $appConfig);
    }

    /**
     * Test ApplicationHandler Exception
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionCode \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
     * @expectedExceptionMessage Not Application found for this appKey
     */
    public function testGetException()
    {
        $applicationHandler = new ApplicationApiHandlerService($this->manager);
        $applicationHandler->get(self::API_KEY);
    }
}