<?php

namespace Araneum\Base\Tests\Unit\Service\Spot;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\UserBundle\Service\Spot\SpotApiCustomerService;

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
    protected $spotOptionServiceMock;
    /**
     * @var SpotApiCustomerService
     */
    protected $spotApiCustomerService;
    /**
     * @var Application
     */
    protected $application;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $em;

    /**
     * Test get data
     */
    public function testGetAllCustomersByPeriod()
    {
        $data = [
            [
                'email' => 'customer1@mail.ru',
                'name' => 'Name1',
            ],
            [
                'email' => 'customer2@mail.ru',
                'name' => 'Name2',
            ],
            [
                'email' => 'customer3@mail.ru',
                'name' => 'Name3',
            ],
        ];
        $this->spotOptionServiceMock->expects($this->once())
            ->method('getCustomersByFilter')
            ->will($this->returnValue($this->responseMock));
        $this->responseMock->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(json_encode($data)));

        $values = $this->spotApiCustomerService->getAllCustomersByPeriod($this->application);

        $this->assertEquals($data, $values);
    }

    /**
     * Set Up
     */
    protected function setUp()
    {
        $this->application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');
        $this->guzzleMock = $this->getMockBuilder('\Guzzle\Service\ClientInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->responseMock = $this->getMockBuilder('\Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder('\Guzzle\Http\Message\EntityEnclosingRequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->spotOptionServiceMock = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Service\SpotOptionService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotApiCustomerService = new SpotApiCustomerService($this->em, $this->spotOptionServiceMock);
    }
}
