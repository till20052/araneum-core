<?php

namespace Araneum\Bundle\CustomerBundle\Tests\Unit\Handler;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\CustomerBundle\Service\CustomerApiHandler;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Araneum\Bundle\CustomerBundle\Entity\Customer;

class CustomerHandlerTest extends BaseController
{
    protected $entityManager;
    protected $repositoryMock;
    protected $doctrineMock;
    protected $applicationManager;

    protected $appKey = ApplicationFixtures::TEST_APP_APP_KEY;

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
    }

    /**
     * Test method ProcessForm with normal data
     *
     * @throws \Araneum\Bundle\CustomerBundle\Exception\InvalidFormException
     */
    public function testProcessFormNormal_True()
    {
        $parameters =
            [
                'firstName' => 'firstName',
                'lastName' => 'lastName',
                'country' => 'country',
                'email' => 'email@email2.com',
                'callback' => true,
                'phone' => '322223'
            ];

        $customer = new Customer;

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $form->expects($this->once())
            ->method('submit')
            ->with($this->equalTo($parameters));

        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $applicationManager = $this->applicationManager;

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($customer);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $customerHandler = new CustomerApiHandler($this->entityManager, $applicationManager);

        $this->assertInstanceOf(
            'Araneum\Bundle\CustomerBundle\Entity\Customer',
            $customerHandler->processForm($parameters, $customer, $form)
        );
    }

    /**
     * @expectedException \Araneum\Bundle\CustomerBundle\Exception\InvalidFormException
     */
    public function testProcessFormException_False()
    {
        $parameters =
            [
                'firstName' => 'firstName',
                'lastName' => 'lastName',
                'country' => 'country',
                'email' => 'email@email.com',
                'callback' => true,
                'phone' => '322223'
            ];

        $customer = new Customer;

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $form->expects($this->once())
            ->method('submit')
            ->with($this->equalTo($parameters));

        $applicationManager = $this->applicationManager;

        $customerHandler = new CustomerApiHandler($this->entityManager, $applicationManager);

        $this->assertInstanceOf(
            'Araneum\Bundle\CustomerBundle\Entity\Customer',
            $customerHandler->processForm($parameters, $customer, $form)
        );
    }

}