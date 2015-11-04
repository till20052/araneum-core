<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Handler;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Customer\CustomerFixtures;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\AgentBundle\Form\CustomerType;
use Araneum\Bundle\AgentBundle\Service\CustomerApiHandlerService;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\MainBundle\Entity\Application;
use Symfony\Component\Form\FormFactory;
use Araneum\Bundle\AgentBundle\Entity\CustomersLog;

class CustomerHandlerTest extends BaseController
{
    const PARAMETER = [
        'firstName' => 'firstName',
        'lastName' => 'lastName',
        'country' => 'country',
        'email' => 'email@email2.com',
        'callback' => true,
        'phone' => '322223'
    ];
    protected $entityManager;
    protected $repositoryMock;
    protected $doctrineMock;
    protected $applicationManager;
    protected $formFactory;
    protected $form;
    protected $customer;
    protected $spotoption;
    protected $container;
    protected $appKey = ApplicationFixtures::TEST_APP_APP_KEY;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->doctrineMock = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->applicationManager = $this->getMockBuilder('Araneum\Bundle\MainBundle\Service\ApplicationManagerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotoption = $this->getMockBuilder('Araneum\Bundle\AgentBundle\Service\SpotOptionService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->customer = new Customer;
    }

    /**
     * Test method ProcessForm with normal data
     *
     * @throws \Araneum\Base\Exception\InvalidFormException
     * @runTestsInSeparateProcesses
     */
    public function testProcessFormNormal_True()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('form.factory'))
            ->will($this->returnValue($this->formFactory));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('doctrine.orm.entity_manager'))
            ->will($this->returnValue($this->entityManager));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo(new CustomerType()), $this->equalTo($this->customer))
            ->will($this->returnValue($this->form));

        $this->form->expects($this->once())
            ->method('submit')
            ->with($this->equalTo(self::PARAMETER));

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->customer);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $customerHandler = new CustomerApiHandlerService($this->container);

        $this->assertInstanceOf(
            'Araneum\Bundle\AgentBundle\Entity\Customer',
            $customerHandler->processForm(self::PARAMETER, $this->customer)
        );
    }

    /**
     * @expectedException \Araneum\Base\Exception\InvalidFormException
     * @runTestsInSeparateProcesses
     */
    public function testProcessFormException_False()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('form.factory'))
            ->will($this->returnValue($this->formFactory));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo(new CustomerType()), $this->equalTo($this->customer))
            ->will($this->returnValue($this->form));

        $this->form->expects($this->once())
            ->method('submit')
            ->with($this->equalTo(self::PARAMETER));

        $customerHandler = new CustomerApiHandlerService($this->container);

        $this->assertInstanceOf(
            'Araneum\Bundle\AgentBundle\Entity\Customer',
            $customerHandler->processForm(self::PARAMETER, $this->customer)
        );
    }

    /**
     * Test login method
     *
     * @runTestsInSeparateProcesses
     */
    public function testLogin()
    {
        $application = new Application();

        $this->applicationManager
            ->expects($this->once())
            ->method('findOneOr404')
            ->with($this->equalTo(['appKey' => $this->appKey]))
            ->will($this->returnValue($application));

        $this->container->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('araneum.main.application.manager'))
            ->will($this->returnValue($this->applicationManager));

        $this->container->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('araneum.agent.spotoption.service'))
            ->will($this->returnValue($this->spotoption));

        $this->spotoption->expects($this->once())
            ->method('login')
            ->with($this->equalTo(CustomerFixtures::TEST_EMAIL), $this->equalTo('password'))
            ->will($this->returnValue(true));

        $customer = $this->getMock('Araneum\Bundle\AgentBundle\Entity\Customer');

        $this->repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(['email' => CustomerFixtures::TEST_EMAIL]))
            ->will($this->returnValue($customer));

        $this->container->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('doctrine.orm.entity_manager'))
            ->will($this->returnValue($this->entityManager));

        $this->entityManager->expects($this->at(0))
            ->method('getRepository')
            ->with($this->equalTo('AraneumAgentBundle:Customer'))
            ->will($this->returnValue($this->repositoryMock));

        $log = new CustomersLog();
        $log->setApplication($application);
        $log->setAction('Login');
        $log->setCustomer($customer);
        $log->setSpotResponse(true);
        $log->setStatus(CustomersLog::STATUS_SUCCESS);

        $this->entityManager->expects($this->at(1))
            ->method('persist')
            ->with($this->equalTo($log));

        $customerHandler = new CustomerApiHandlerService($this->container);
        $customerHandler->login(CustomerFixtures::TEST_EMAIL, 'password', $this->appKey);
    }

}