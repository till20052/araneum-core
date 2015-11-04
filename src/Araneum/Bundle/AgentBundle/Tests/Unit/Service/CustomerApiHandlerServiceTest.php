<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Service;

use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\AgentBundle\Service\CustomerApiHandlerService;
use Doctrine\Common\Collections\ArrayCollection;

class CustomerApiHandlerServiceTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $application;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $customer;

	/**
	 * @var CustomerApiHandlerService
	 */
	private $service;

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	private function entityManager()
	{
		$entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$applicationRepository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
			->setMethods(['findOneByAppKey'])
			->disableOriginalConstructor()
			->getMock();

		$this->application = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Application')
			->disableOriginalConstructor()
			->getMock();

		$applicationRepository->expects($this->any())
			->method('findOneByAppKey')
			->with($this->equalTo('123456789'))
			->will($this->returnValue($this->application));

		$entityManager->expects($this->at(0))
			->method('getRepository')
			->with($this->equalTo('AraneumMainBundle:Application'))
			->will($this->returnValue($applicationRepository));

		$customerRepository = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Repository\CustomerRepository')
			->setMethods(['findOneByEmail'])
			->disableOriginalConstructor()
			->getMock();

		$this->customer = $customer = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Entity\Customer')
			->disableOriginalConstructor()
			->getMock();

		$customerRepository->expects($this->any())
			->method('findOneByEmail')
			->with($this->logicalOr(
				$this->equalTo('user@test.com'),
				$this->equalTo('not_mocked_value@test.com')
			))
			->will($this->returnCallback(function ($email) use ($customer) {
				if ($email != 'user@test.com') {
					return null;
				}

				return $customer;
			}));

		$entityManager->expects($this->at(1))
			->method('getRepository')
			->with($this->equalTo('AraneumAgentBundle:Customer'))
			->will($this->returnValue($customerRepository));

		$customerLog = (new CustomerLog())
			->setAction('reset_password')
			->setApplication($this->application)
			->setCustomer($customer)
			->setSpotResponse(true)
			->setStatus(CustomerLog::STATUS_OK);

		$entityManager->expects($this->any())
			->method('persist')
			->with($this->equalTo($customerLog));

		$entityManager->expects($this->any())
			->method('flush');

		return $entityManager;
	}

	/**
	 * @inheritdoc
	 */
	protected function setUp()
	{
		$container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
			->disableOriginalConstructor()
			->getMock();

		$entityManager = $this->entityManager();

		$spotOptionService = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Service\SpotOptionService')
			->disableOriginalConstructor()
			->getMock();

		$spotOptionService->expects($this->any())
			->method('resetPassword')
			->with(
				$this->equalTo('user@test.com'),
				$this->equalTo('current_password'),
				$this->equalTo('new_password')
			)
			->will($this->returnValue(true));

		$container->expects($this->any())
			->method('get')
			->with($this->logicalOr(
				$this->equalTo('doctrine.orm.entity_manager'),
				$this->equalTo('araneum.agent.spotoption.service')
			))
			->will($this->returnCallback(function ($arg) use ($entityManager, $spotOptionService) {
				switch ($arg) {
					case 'doctrine.orm.entity_manager':
						return $entityManager;
						break;

					case 'araneum.agent.spotoption.service':
						return $spotOptionService;
						break;
				}
			}));

		$this->service = new CustomerApiHandlerService($container);
	}

	/**
	 * Test reset customer password
	 *
	 * @throws \Doctrine\ORM\EntityNotFoundException
	 * @throws \Exception
	 */
	public function testResetPassword()
	{
		$this->application->expects($this->any())
			->method('getCustomers')
			->will($this->returnValue(new ArrayCollection([$this->customer])));

		$this->assertEquals(
			CustomerLog::STATUS_OK,
			$this->service->resetPassword('123456789', 'user@test.com', 'current_password', 'new_password')
		);
	}

	/**
	 * Test reset customer password in case if application does not contain customer
	 *
	 * @expectedException \Exception
	 */
	public function testResetPasswordInCaseException()
	{
		$this->application->expects($this->any())
			->method('getCustomers')
			->will($this->returnValue(new ArrayCollection()));

		$this->service->resetPassword('123456789', 'user@test.com', 'current_password', 'new_password');
	}

	/**
	 * Test reset customer password in case if customer does not exists
	 *
	 * @expectedException \Doctrine\ORM\EntityNotFoundException
	 */
	public function testResetPasswordInCaseEntityNotFoundException()
	{
		$this->service->resetPassword('123456789', 'not_mocked_value@test.com', 'current_password', 'new_password');
	}
}