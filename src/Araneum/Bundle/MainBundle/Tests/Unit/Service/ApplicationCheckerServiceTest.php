<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Bundle\AgentBundle\Service\AgentLoggerService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Service\ApplicationCheckerService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

class ApplicationCheckerServiceTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $entityManager;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $guzzleClient;

	/**
	 * @var ApplicationCheckerService
	 */
	private $service;

	/**
	 * Mock EntityManager
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockEntityManager()
	{
		$entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$entityManager->expects($this->any())
			->method('flush');

		return $entityManager;
	}

	/**
	 * Mock Guzzle Client Service
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockGuzzleClient()
	{
		$response = $this->getMockBuilder('\Guzzle\Http\Message\Response')
			->setConstructorArgs([Response::HTTP_OK])
			->getMock();
		$response->expects($this->any())
			->method('isSuccessful')
			->will($this->returnValue(true));

		$request = $this->getMockBuilder('\Guzzle\Http\Message\RequestInterface')
			->getMock();
		$request->expects($this->any())
			->method('send')
			->will($this->returnValue($response));

		$client = $this->getMockBuilder('\Guzzle\Service\Client')
			->disableOriginalConstructor()
			->getMock();
		$client->expects($this->any())
			->method('get')
			->with($this->equalTo('http://localhost'))
			->will($this->returnValue($request));

		return $client;
	}

	/**
	 * Mock Agent Logger Service
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockAgentLoggerService()
	{
		$loggerService = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Service\AgentLoggerService')
			->disableOriginalConstructor()
			->getMock();
		$loggerService->expects($this->any())
			->method('logConnection');

		return $loggerService;
	}

	/**
	 * Mock Connection entity
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockConnection()
	{
		$entity = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Connection')
			->disableOriginalConstructor()
			->getMock();
		$entity->expects($this->once())
			->method('getHost')
			->will($this->returnValue('127.0.0.1'));
		$entity->expects($this->once())
			->method('setStatus')
			->with($this->equalTo(Connection::STATUS_OK));

		return $entity;
	}

	/**
	 * Mock Application entity
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockApplication()
	{
		$entity = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Application')
			->disableOriginalConstructor()
			->getMock();
		$entity->expects($this->once())
			->method('isUseSsl')
			->will($this->returnValue(false));
		$entity->expects($this->once())
			->method('getDomain')
			->will($this->returnValue('localhost'));
		$entity->expects($this->once())
			->method('setStatus')
			->with($this->equalTo(Application::STATUS_OK));

		return $entity;
	}

	/**
	 * @inheritdoc
	 */
	protected function setUp()
	{
		$this->entityManager = $this->mockEntityManager();
		$this->guzzleClient = $this->mockGuzzleClient();

		$this->service = new ApplicationCheckerService($this->entityManager, $this->guzzleClient);
		$this->service->setAgentLogger($this->mockAgentLoggerService());
	}

	/**
	 * Test connection state checking
	 */
	public function testCheckConnection()
	{
		$repository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ConnectionRepository')
			->disableOriginalConstructor()
			->getMock();
		$repository->expects($this->once())
			->method('find')
			->with($this->equalTo(777))
			->will($this->returnValue($this->mockConnection()));

		$this->entityManager->expects($this->any())
			->method('getRepository')
			->with($this->equalTo('AraneumMainBundle:Connection'))
			->will($this->returnValue($repository));

		$this->assertEquals(Connection::STATUS_OK, $this->service->checkConnection(777));
	}

	/**
	 * Test application state checking
	 */
	public function testCheckApplication()
	{
		$repository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
			->disableOriginalConstructor()
			->getMock();
		$repository->expects($this->once())
			->method('find')
			->with($this->equalTo(777))
			->will($this->returnValue($this->mockApplication()));

		$this->entityManager->expects($this->any())
			->method('getRepository')
			->with($this->equalTo('AraneumMainBundle:Application'))
			->will($this->returnValue($repository));

		$this->assertEquals(Application::STATUS_OK, $this->service->checkApplication(777));
	}

	/**
	 * Test cluster state checking
	 */
	public function testCheckCluster()
	{
		$entity = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Cluster')
			->disableOriginalConstructor()
			->getMock();
		$entity->expects($this->atLeastOnce())
			->method('getApplications')
			->will($this->returnValue(new ArrayCollection([$this->mockApplication()])));
		$entity->expects($this->atLeastOnce())
			->method('getHosts')
			->will($this->returnValue(new ArrayCollection([$this->mockConnection()])));
		$entity->expects($this->once())
			->method('setStatus')
			->with($this->equalTo(Cluster::STATUS_OK));

		$repository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ClusterRepository')
			->disableOriginalConstructor()
			->getMock();
		$repository->expects($this->once())
			->method('find')
			->with($this->equalTo(777))
			->will($this->returnValue($entity));

		$this->entityManager->expects($this->any())
			->method('getRepository')
			->with($this->equalTo('AraneumMainBundle:Cluster'))
			->will($this->returnValue($repository));

		$this->assertEquals(Application::STATUS_OK, $this->service->checkCluster(777));
	}
}