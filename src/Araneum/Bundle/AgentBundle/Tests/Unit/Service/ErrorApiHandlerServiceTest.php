<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Service;

use Araneum\Bundle\AgentBundle\Entity\Error;
use Araneum\Bundle\AgentBundle\Service\ErrorApiHandlerService;
use Araneum\Bundle\MainBundle\Entity\Application;

/**
 * Class ErrorApiHandlerServiceTest
 *
 * @package Araneum\Bundle\AgentBundle\Tests\Unit\Service
 */
class ErrorApiHandlerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Entity manager
     *
     * @var
     */
    protected $entityManagerMock;

    /**
     * Form factory
     *
     * @var
     */
    protected $formFactoryMock;

    /**
     * Application manager
     *
     * @var
     */
    protected $applicationManagerMock;

    /**
     * Form
     *
     * @var
     */
    protected $formMock;

    /**
     * Service
     *
     * @var ErrorApiHandlerService
     */
    protected $errorApiHandlerService;

    /**
     * Application
     *
     * @var
     */
    protected $application;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->application = new Application();

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

        $this->applicationManagerMock = $this->getMockBuilder(
            '\Araneum\Bundle\MainBundle\Service\ApplicationManagerService'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->applicationManagerMock->expects($this->once())
            ->method('findOneOr404')
            ->will($this->returnValue($this->application));

        $container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();

        $this->errorApiHandlerService = new ErrorApiHandlerService($container);
    }

    /**
     * Test post valid form
     */
    public function testPostFormValidOk()
    {
        $parameters = ['test' => 'test'];

        $error = new Error();
        $error->setApplication($this->application);

        $this->formMock->expects($this->once())
            ->method('submit')
            ->with($this->equalTo($parameters));

        $this->formMock->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($error));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->assertInstanceOf(
            'Araneum\Bundle\AgentlBundle\Entity\Error',
            $this->errorApiHandlerService->post('appKey', $parameters)
        );
    }

    /**
     * Test throw exception
     *
     * @expectedException \Araneum\Base\Exception\InvalidFormException
     */
    public function testPostFormNotValidException()
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

        $this->errorApiHandlerService->post('appKey', $parameters);
    }

}
