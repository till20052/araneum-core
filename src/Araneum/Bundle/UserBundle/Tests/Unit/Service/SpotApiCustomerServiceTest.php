<?php
namespace Araneum\Bundle\UserBundle\Tests\Unit\Service;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\UserBundle\Service\Spot\SpotApiCustomerService;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use fixtures\App;

/**
 * Class SpotApiCustomerServiceTest
 *
 * @package Araneum\Bundle\UserBundle\Tests\Unit\Service
 */
class SpotApiCustomerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockSpotOptionService;

    /**
     * @var SpotApiCustomerService
     */
    private $service;


    public function testGetAllCustomersByPetiod()
    {
        $this->service->getAllCustomersByPeriod($this->application, 'P1Y');
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->em = $this->mockEntityManager();

        $this->mockSpotOptionService = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Service\SpotOptionService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new SpotApiCustomerService($this->em,$this->mockSpotOptionService);
    }

    /**
     * Mock EntityManager
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockEntityManager()
    {
        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->any())
            ->method('flush');

        return $entityManager;
    }

    /**
     * @inheritdoc
     */
    private function setData()
    {
        $this->application = (new Application())
            ->setSpotApiUrl('testest.com.ua')
            ->setSpotApiUser('fsdfsd')
            ->setSpotApiPassword('sdfsdfsdfsdf')
        ;
    }
}