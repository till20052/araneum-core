<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Service;

use Araneum\Bundle\AgentBundle\AraneumAgentBundle;
use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\AgentBundle\Event\LeadEvent;
use Araneum\Bundle\AgentBundle\Form\Type\LeadType;
use Araneum\Bundle\AgentBundle\Service\LeadApiHandlerService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;

/**
 * Class LeadApiHandlerServiceTest
 *
 * @package Araneum\Bundle\AgentBundle\Tests\Unit\Service
 */
class LeadApiHandlerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $applicationManagerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcherMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityManagerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $formMock;
    /**
     * @var LeadApiHandlerService
     */
    private $apiHandler;

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
                'appKey' => md5(microtime(true)),
            ],
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
        $application = new Application();
        $lead = new Lead();
        $lead->setAppKey('testAppKey');
        $lead->setApplication($application);

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->formFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(new LeadType()),
                $this->callback(
                    function ($lead) {
                        $lead->setAppKey('testAppKey');

                        return true;
                    }
                )
            )
            ->will($this->returnValue($this->formMock));

        $this->applicationManagerMock
            ->expects($this->once())
            ->method('findOneOr404')
            ->with($this->equalTo(['appKey' => 'testAppKey']))
            ->will($this->returnValue($application));

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($lead));
        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->dispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->equalTo(AraneumAgentBundle::EVENT_LEAD_NEW),
                $this->equalTo((new LeadEvent($lead)))
            );

        $this->assertEquals(
            $lead,
            $this->apiHandler->create(['appKey' => 'testAppKey'])
        );
    }

    /**
     * Test create method in LeadApiHandlerService in case if form not valid
     *
     * @expectedException \Araneum\Base\Exception\InvalidFormException
     */
    public function testCreateException()
    {
        $this->applicationManagerMock
            ->expects($this->once())
            ->method('findOneOr404')
            ->will($this->returnValue(new Application()));

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->formFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->formMock));

        $this->apiHandler->create(['appKey' => 'testAppKey']);
    }

    /**
     * Initialization
     */
    protected function setUp()
    {
        $this->applicationManagerMock = $this
            ->getMockBuilder('\Araneum\Bundle\MainBundle\Service\ApplicationManagerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcherMock = $this->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiHandler = new LeadApiHandlerService(
            $this->entityManager(),
            $this->formFactory(),
            $this->applicationManagerMock,
            $this->dispatcherMock
        );
    }

    /**
     * Mock EntityManager
     *
     * @return EntityManager
     */
    private function entityManager()
    {
        $this->entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Repository\LeadRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo('AraneumAgentBundle:Lead'))
            ->will($this->returnValue($this->repository));

        return $this->entityManagerMock;
    }

    /**
     * Mock FormFactory
     *
     * @return FormFactory
     */
    private function formFactory()
    {
        $this->formFactoryMock = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formMock = $this->getMockBuilder('\Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formMock->expects($this->any())
            ->method('submit')
            ->will($this->returnValue($this->formMock));

        return $this->formFactoryMock;
    }
}
