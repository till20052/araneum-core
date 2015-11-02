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
    protected $entityManager;
    protected $repositoryMock;
    protected $doctrineMock;
    protected $applicationManager;
    protected $formFactory;
    protected $form;
    protected $customer;
    protected $spotoption;

    protected $appKey = ApplicationFixtures::TEST_APP_APP_KEY;

    const PARAMETER = [
        'firstName' => 'firstName',
        'lastName' => 'lastName',
        'country' => 'country',
        'email' => 'email@email2.com',
        'callback' => true,
        'phone' => '322223'
    ];

    /**
     * Set Up
     */
    public function setUp()
    {
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
        $applicationManager = $this->applicationManager;

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo(new CustomerType()), $this->equalTo($this->customer))
            ->will($this->returnValue($this->form));


        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->customer);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $customerHandler = new CustomerApiHandlerService(
            $this->entityManager,
            $applicationManager,
            $this->formFactory,
            $this->spotoption
        );

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
        $applicationManager = $this->applicationManager;

        $customerHandler = new CustomerApiHandlerService(
            $this->entityManager,
            $applicationManager,
            $this->formFactory,
            $this->spotoption
        );

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
        $this->spotoption->expects($this->once())
            ->method('login')
            ->with($this->equalTo(CustomerFixtures::TEST_EMAIL), $this->equalTo('password'))
            ->will($this->returnValue(true));

        $application = new Application();
        $this->applicationManager
            ->expects($this->once())
            ->method('findOneOr404')
            ->with($this->equalTo(['appKey' => $this->appKey]))
            ->will($this->returnValue($application));

        $customer = $this->getMock('Araneum\Bundle\AgentBundle\Entity\Customer');

        $this->repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(['email' => CustomerFixtures::TEST_EMAIL]))
            ->will($this->returnValue($customer));

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

        $customerHandler = new CustomerApiHandlerService(
            $this->entityManager,
            $this->applicationManager,
            $this->formFactory,
            $this->spotoption
        );
        $customerHandler->login(CustomerFixtures::TEST_EMAIL, 'password', $this->appKey);
    }


}