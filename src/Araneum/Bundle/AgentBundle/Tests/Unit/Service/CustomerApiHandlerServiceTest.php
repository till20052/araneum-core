<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Fixtures\Agent\CustomerFixtures;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\AgentBundle\Form\Type\CustomerType;
use Araneum\Bundle\AgentBundle\Service\CustomerApiHandlerService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CustomerApiHandlerServiceTest
 *
 * @package Araneum\Bundle\AgentBundle\Tests\Unit\Handler
 */
class CustomerApiHandlerServiceTest extends \PHPUnit_Framework_TestCase
{
    const PARAMETER = [
        'firstName' => 'firstName',
        'lastName' => 'lastName',
        'country' => 'country',
        'email' => 'email@email2.com',
        'callback' => true,
        'phone' => '322223',
    ];
    protected $entityManagerMock;
    protected $repositoryMock;
    protected $doctrineMock;
    protected $applicationManagerMock;
    protected $formFactoryMock;
    protected $form;
    protected $customer;
    protected $spotOptionServiceMock;
    protected $appKey = ApplicationFixtures::TEST_APP_APP_KEY;
    protected $dispatcherMock;
    /**
     * @var CustomerApiHandlerService
     */
    protected $customerApiHandlerService;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->entityManagerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcherMock = $this
            ->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->doctrineMock = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->applicationManagerMock = $this->getMockBuilder(
            'Araneum\Bundle\MainBundle\Service\ApplicationManagerService'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotOptionServiceMock = $this->getMockBuilder('Araneum\Bundle\AgentBundle\Service\SpotOptionService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formFactoryMock = $this->getMockBuilder('Symfony\Component\Form\FormFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->customer = $this->getMock('\Araneum\Bundle\AgentBundle\Entity\Customer');
        $this->customer->method('getId')->will($this->returnValue(1));

        $this->customerApiHandlerService = new CustomerApiHandlerService(
            $this->applicationManagerMock,
            $this->entityManagerMock,
            $this->dispatcherMock,
            $this->formFactoryMock,
            $this->spotOptionServiceMock
        );
    }

    /**
     * Test method ProcessForm with normal data
     *
     * @throws \Araneum\Base\Exception\InvalidFormException
     * @runTestsInSeparateProcesses
     */
    public function testProcessFormNormalTrue()
    {
        $this->formFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo(new CustomerType()), $this->equalTo($this->customer))
            ->will($this->returnValue($this->form));

        $this->form->expects($this->once())
            ->method('submit')
            ->with($this->equalTo(self::PARAMETER));

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->customer);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->assertEquals(
            ['id' => 1],
            $this->customerApiHandlerService->processForm(self::PARAMETER, $this->customer)
        );
    }

    /**
     * @expectedException \Araneum\Base\Exception\InvalidFormException
     * @runTestsInSeparateProcesses
     */
    public function testProcessFormExceptionFalse()
    {
        $this->formFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo(new CustomerType()), $this->equalTo($this->customer))
            ->will($this->returnValue($this->form));

        $this->form->expects($this->once())
            ->method('submit')
            ->with($this->equalTo(self::PARAMETER));

        $this->assertInstanceOf(
            'Araneum\Bundle\AgentBundle\Entity\Customer',
            $this->customerApiHandlerService->processForm(self::PARAMETER, $this->customer)
        );
    }

    /**
     * Test login method
     *
     * @runTestsInSeparateProcesses
     */
    public function testLogin()
    {
        $customer = $this->getMock('Araneum\Bundle\AgentBundle\Entity\Customer');
        $application = new Application();
        $application->setCustomers(new ArrayCollection([$customer]));

        $this->applicationManagerMock
            ->expects($this->once())
            ->method('findOneOr404')
            ->with($this->equalTo(['appKey' => $this->appKey]))
            ->will($this->returnValue($application));

        $this->spotOptionServiceMock->expects($this->once())
            ->method('login')
            ->with($this->equalTo($customer))
            ->will($this->returnValue(true));

        $this->repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(['email' => CustomerFixtures::TEST_EMAIL]))
            ->will($this->returnValue($customer));

        $this->entityManagerMock->expects($this->at(0))
            ->method('getRepository')
            ->with($this->equalTo('AraneumAgentBundle:Customer'))
            ->will($this->returnValue($this->repositoryMock));

        $this->customerApiHandlerService->login(CustomerFixtures::TEST_EMAIL, 'password', $this->appKey);
    }
}
