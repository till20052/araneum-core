<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Handler;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\AgentBundle\Form\CustomerType;
use Araneum\Bundle\AgentBundle\Service\CustomerApiHandlerService;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Symfony\Component\Form\FormFactory;

class CustomerHandlerTest extends BaseController
{
    protected $entityManager;
    protected $repositoryMock;
    protected $doctrineMock;
    protected $applicationManager;
    protected $formFactory;
    protected $form;
    protected $customer;

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

        $this->formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->customer = new Customer;

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo(new CustomerType()), $this->equalTo($this->customer))
            ->will($this->returnValue($this->form));
    }

    /**
     * Test method ProcessForm with normal data
     *
     * @throws \Araneum\Base\Exception\InvalidFormException
     */
    public function testProcessFormNormal_True()
    {
        $applicationManager = $this->applicationManager;

        $this->form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->customer);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $customerHandler = new CustomerApiHandlerService($this->entityManager, $applicationManager, $this->formFactory);

        $this->assertInstanceOf(
            'Araneum\Bundle\AgentBundle\Entity\Customer',
            $customerHandler->processForm(self::PARAMETER, $this->customer)
        );
    }

    /**
     * @expectedException \Araneum\Base\Exception\InvalidFormException
     */
    public function testProcessFormException_False()
    {
        $applicationManager = $this->applicationManager;

        $customerHandler = new CustomerApiHandlerService($this->entityManager, $applicationManager, $this->formFactory);

        $this->assertInstanceOf(
            'Araneum\Bundle\AgentBundle\Entity\Customer',
            $customerHandler->processForm(self::PARAMETER, $this->customer)
        );
    }

}