<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Service;

use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\AgentBundle\Form\Type\LeadType;
use Araneum\Bundle\AgentBundle\Service\LeadApiHandlerService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;

class LeadApiHandlerServiceTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $repository;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	private $form;

	/**
	 * @var LeadApiHandlerService
	 */
	private $apiHandler;

	/**
	 * Mock EntityManager
	 *
	 * @return EntityManager
	 */
	private function entityManager()
	{
		$entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$this->repository = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Repository\LeadRepository')
			->disableOriginalConstructor()
			->getMock();

		$entityManager->expects($this->any())
			->method('getRepository')
			->with($this->equalTo('AraneumAgentBundle:Lead'))
			->will($this->returnValue($this->repository));

		$entityManager->expects($this->any())
			->method('persist')
			->with($this->equalTo(new Lead()));

		$entityManager->expects($this->any())
			->method('flush');

		return $entityManager;
	}

	/**
	 * Mock FormFactory
	 *
	 * @return FormFactory
	 */
	private function formFactory()
	{
		$formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')
			->disableOriginalConstructor()
			->getMock();

		$this->form = $this->getMockBuilder('\Symfony\Component\Form\Form')
			->disableOriginalConstructor()
			->getMock();

		$this->form->expects($this->any())
			->method('submit')
			->with($this->equalTo([]))
			->will($this->returnValue($this->form));

		$formFactory->expects($this->any())
			->method('create')
			->with(
				$this->equalTo(new LeadType()),
				$this->equalTo(new Lead())
			)
			->will($this->returnValue($this->form));

		return $formFactory;
	}

	/**
	 * Initialization
	 */
	protected function setUp()
	{
		$this->apiHandler = new LeadApiHandlerService(
			$this->entityManager(),
			$this->formFactory()
		);
	}

	/**
	 * Test find method in LeadApiHandlerService
	 */
	public function testFind()
	{
		$expected = [
			[
				'firstName' => 'Ferrari',
				'lastName' => 'Italia458',
				'country' => rand(1, 239),
				'email' => 'ferrari.italia458@test.com',
				'phone' => '380507894561',
				'appKey' => md5(microtime(true))
			]
		];

		$this->repository
			->expects($this->any())
			->method('findByFilter')
			->will($this->returnValue($expected));

		$this->assertEquals(
			$expected,
			$this->apiHandler->find()
		);
	}

	/**
	 * Test create method in LeadApiHandlerService
	 */
	public function testCreate()
	{
		$this->form->expects($this->once())
			->method('isValid')
			->will($this->returnValue(true));

		$this->assertEquals(
			new Lead(),
			$this->apiHandler->create([])
		);
	}

	/**
	 * Test create method in LeadApiHandlerService in case if form not valid
	 *
	 * @expectedException \Araneum\Base\Exception\InvalidFormException
	 */
	public function testCreateException()
	{
		$this->form->expects($this->once())
			->method('isValid')
			->will($this->returnValue(false));

		$this->apiHandler->create([]);
	}
}