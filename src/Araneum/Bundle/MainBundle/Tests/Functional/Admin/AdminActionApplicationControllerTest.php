<?php
namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Controller\AdminApplicationController;
use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Guzzle\Service\Description\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\Dump\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\All;
use Araneum\Base\Tests\Controller\BaseAdminController;

/**
 * Class AdminActionApplicationControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class AdminActionApplicationControllerTest extends BaseController
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var AdminApplicationController
     */
    private $controller;

    /**
     * @var ApplicationRepository
     */
    private $repositoryApplication;

    /**
     * @var Container
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        /**
         * @var AdminApplicationController
         */
        $this->controller = new AdminApplicationController();

        /**
         * @var Request
         */
        $this->request = new Request();
        $this->request->setMethod('POST');
        $this->request->request->set('data', [1]);

        /**
         * @var ValidatorInterface
         */
        $this->validator = $this->getMock(
            'Symfony\Component\Validator\Validator\ValidatorInterface'
        );
        $this->validator->expects($this->any())
            ->method('validate')
            ->with([1], new All([new Regex('/^\d+$/')]))
            ->will($this->returnValue([]));

        /**
         * @var ApplicationRepository
         */
        $this->repositoryApplication = $this->getMockBuilder('Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var Container
         */
        $this->container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();

        $this->client = self::createAdminAuthorizedClient('admin');

        /**
         * @var Router router
         */
        $this->router = $this->client->getContainer()->get('router');
    }

    /**
     * Test Enable Action
     */
    public function testEnableAction()
    {
        $this->repositoryApplication->expects($this->once())
            ->method('updateEnabled')
            ->with([1], true)
            ->will($this->returnValue(true));
        $this->getDoctrine($this->repositoryApplication);

        $this->controller->setContainer($this->getContainer());
        $resultJson = $this->controller->enableAction($this->request);
        $this->assertEquals('Success', json_decode($resultJson->getContent()));
    }

    /**
     * Test Disable Action
     */
    public function testDisabledAction()
    {
        $this->repositoryApplication->expects($this->once())
            ->method('updateEnabled')
            ->with([1], false)
            ->will($this->returnValue(true));
        $this->getDoctrine($this->repositoryApplication);

        $this->controller->setContainer($this->getContainer());
        $resultJson = $this->controller->disableAction($this->request);
        $this->assertEquals('Success', json_decode($resultJson->getContent()));
    }

    /**
     * Test Check Status Action
     */
    public function testCheckStatusAction()
    {
        $this->getMockApplicationCheckerService();
        $this->controller->setContainer($this->getContainer());
        $resultJson = $this->controller->checkStatusAction($this->request);
        $this->assertEquals('Success', json_decode($resultJson->getContent()));
    }

    /**
     * Mock Repository ApplicationCheckerService
     */
    private function getMockApplicationCheckerService()
    {
        $serviceApplicationCheck = $this->getMockBuilder('Araneum\Bundle\MainBundle\Service\ApplicationCheckerService')
            ->disableOriginalConstructor()
            ->getMock();

        $serviceApplicationCheck->expects($this->any())
            ->method('checkApplication')
            ->with(1)
            ->will($this->returnValue(0));

        $this->container->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('araneum.main.application.checker'))
            ->will($this->returnValue($serviceApplicationCheck));
    }

    /**
     * Mock Doctrine
     *
     * @param $repository
     */
    private function getDoctrine($repository)
    {
        $mockManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $mockManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));
        $doctrine = $this->getMock('Doctrine', array('getManager'));
        $doctrine->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($mockManager));

        $this->container->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('doctrine'))
            ->will($this->returnValue($doctrine));
    }

    /**
     * Mock DI Container
     *
     * @return Container
     */
    private function getContainer()
    {
        $this->container->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('validator'))
            ->will($this->returnValue($this->validator));

        return $this->container;
    }
}
