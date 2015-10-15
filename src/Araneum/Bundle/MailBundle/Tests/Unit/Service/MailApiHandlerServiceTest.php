<?php

namespace Araneum\Bundle\MailBundle\Tests\Unit\Service;

use Araneum\Bundle\MailBundle\Entity\Mail;
use Araneum\Bundle\MailBundle\Service\MailApiHandlerService;
use Araneum\Bundle\MainBundle\Entity\Application;

class MailApiHandlerServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $entityManagerMock;
    protected $formFactoryMock;
    protected $applicationManagerMock;
    protected $formMock;
    /**
     * @var MailApiHandlerService
     */
    protected $mailApiHandlerService;
    protected $application;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->entityManagerMock = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formMock = $this->getMockBuilder('\Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formFactoryMock = $this
            ->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->formMock));

        $this->applicationManagerMock = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Service\ApplicationManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->application = new Application();
        $this->applicationManagerMock->expects($this->once())
            ->method('findOneOr404')
            ->will($this->returnValue($this->application));

        $this->mailApiHandlerService = new MailApiHandlerService(
            $this->entityManagerMock,
            $this->formFactoryMock,
            $this->applicationManagerMock
        );
    }

    /**
     * Test post valid form
     */
    public function testPostFormValid_Ok()
    {
        $parameters = ['test' => 'test'];

        $mail = new Mail();
        $mail->setApplication($this->application);

        $this->formMock->expects($this->once())
            ->method('submit')
            ->with($this->equalTo($parameters));

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($mail));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->assertInstanceOf(
            'Araneum\Bundle\MailBundle\Entity\Mail',
            $this->mailApiHandlerService->post('appKey', $parameters)
        );
    }

    /**
     * Test throw exception
     *
     * @expectedException \Araneum\Base\Exception\InvalidFormException
     */
    public function testPostFormNotValid_Exception()
    {
        $parameters = ['test' => 'test'];

        $this->formMock->expects($this->once())
            ->method('submit')
            ->with($this->equalTo($parameters));

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->entityManagerMock->expects($this->never())
            ->method('persist');

        $this->entityManagerMock->expects($this->never())
            ->method('flush');

        $this->mailApiHandlerService->post('appKey', $parameters);
    }
}