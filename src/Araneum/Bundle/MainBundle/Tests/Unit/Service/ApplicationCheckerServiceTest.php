<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Bundle\MainBundle\Service\ApplicationCheckerService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

class ApplicationCheckerServiceTest extends \PHPUnit_Framework_TestCase
{
	private $entityManager;

	private $applicationRepository;

	private $application;

	private $client;

	private $response;

	/**
	 * @var ApplicationCheckerService
	 */
	private $service;

	protected function setUp()
	{
		$this->application = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Application')
			->disableOriginalConstructor()
			->getMock();
		$this->application->expects($this->any())
			->method('getId')
			->will($this->returnValue(777));
		$this->application->expects($this->any())
			->method('isUseSsl')
			->will($this->returnValue(false));
		$this->application->expects($this->any())
			->method('getDomain')
			->will($this->returnValue('localhost'));
		$this->application->expects($this->any())
			->method('setStatus')
			->with($this->equalTo(true));
		$this->application->expects($this->any())
			->method('getStatus')
			->will($this->returnValue(true));

		$this->applicationRepository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
			->disableOriginalConstructor()
			->getMock();
		$this->applicationRepository->expects($this->any())
			->method('find')
			->with($this->equalTo(777))
			->will($this->returnValue($this->application));

		$this->entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();
		$this->entityManager->expects($this->any())
			->method('flush');

		$this->response = $this->getMockBuilder('\Guzzle\Http\Message\Response')
			->setConstructorArgs([Response::HTTP_OK])
			->getMock();
		$this->response->expects($this->any())
			->method('getStatusCode')
			->will($this->returnValue(Response::HTTP_OK));

		$request = $this->getMockBuilder('\Guzzle\Http\Message\RequestInterface')
			->getMock();
		$request->expects($this->any())
			->method('send')
			->will($this->returnValue($this->response));

		$this->client = $this->getMockBuilder('\Guzzle\Service\Client')
			->disableOriginalConstructor()
			->getMock();
		$this->client->expects($this->any())
			->method('get')
			->with($this->equalTo('http://localhost'))
			->will($this->returnValue($request));

		$this->service = new ApplicationCheckerService($this->entityManager, $this->client);
	}

	public function testCheckConnection()
	{
		$entity = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Connection')
			->disableOriginalConstructor()
			->getMock();
		$entity->expects($this->once())
			->method('getHost')
			->will($this->returnValue('127.0.0.1'));
		$entity->expects($this->once())
			->method('setStatus')
			->with($this->equalTo(true));
		$entity->expects($this->once())
			->method('getStatus')
			->will($this->returnValue(true));

		$repository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ConnectionRepository')
			->disableOriginalConstructor()
			->getMock();
		$repository->expects($this->once())
			->method('find')
			->with($this->equalTo(777))
			->will($this->returnValue($entity));

		$this->entityManager->expects($this->any())
			->method('getRepository')
			->with($this->equalTo('AraneumMainBundle:Connection'))
			->will($this->returnValue($repository));

		$this->assertTrue($this->service->checkConnection(777));
	}

	public function testCheckApplication()
	{
		$this->entityManager->expects($this->any())
			->method('getRepository')
			->with($this->equalTo('AraneumMainBundle:Application'))
			->will($this->returnValue($this->applicationRepository));

		$this->assertTrue($this->service->checkApplication(777));
	}

	public function testCheckCluster()
	{
		$cluster = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Cluster')
			->disableOriginalConstructor()
			->getMock();
		$cluster->expects($this->atLeastOnce())
			->method('getApplications')
			->will($this->returnValue(new ArrayCollection([$this->application])));
		$cluster->expects($this->once())
			->method('setStatus')
			->with($this->equalTo(true));
		$cluster->expects($this->once())
			->method('getStatus')
			->will($this->returnValue(true));

		$clusterRepository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ClusterRepository')
			->disableOriginalConstructor()
			->getMock();
		$clusterRepository->expects($this->once())
			->method('find')
			->with($this->equalTo(777))
			->will($this->returnValue($cluster));

		$this->entityManager->expects($this->at(0))
			->method('getRepository')
			->with($this->equalTo('AraneumMainBundle:Cluster'))
			->will($this->returnValue($clusterRepository));
		$this->entityManager->expects($this->at(1))
			->method('getRepository')
			->with($this->equalTo('AraneumMainBundle:Application'))
			->will($this->returnValue($this->applicationRepository));

		$this->assertTrue($this->service->checkCluster(777));
	}
}