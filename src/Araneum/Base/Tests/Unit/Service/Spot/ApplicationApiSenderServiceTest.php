<?php

namespace Araneum\Base\Tests\Unit\Service\Spot;

use Araneum\Base\Service\Application\ApplicationApiSenderService;

/**
 * Class ApplicationApiSenderServiceTest
 *
 * @package Araneum\Base\Service\Spot
 */
class ApplicationApiSenderServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $guzzleMock;
    /**
     * @var ApplicationApiSenderService
     */
    protected $applicationApiSenderService;

    protected $requestData = [
        'key' => 'value',
        'key2' => 'value',
        'key3' => 'value',
    ];
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $em;

    /**
     *  Test normal work of method
     */
    public function testSendNormalEmptyData()
    {
        $helper = [
            'url' => 'http://apiUrl.com',
            'customerId' => '1',
        ];

        $this->guzzleMock->expects($this->once())
            ->method('setBaseUrl')
            ->will($this->returnValue('apiUrl'));

        $this->guzzleMock->expects($this->once())
            ->method('post')
            ->with(
                null,
                null,
                $this->requestData
            )
            ->will($this->returnValue($this->requestMock));

        $this->requestMock->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->responseMock));

        $this->responseMock->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(null));

        $answer = $this->applicationApiSenderService->send(
            $this->requestData,
            $helper
        );

        $this->assertInstanceOf('Exception', $answer);
    }

    /**
     *  Test normal work of method
     */
    public function testSendNormalData()
    {
        $customer = $this->getMock('\Araneum\Bundle\AgentBundle\Entity\Customer');

        $helper = [
            'url' => 'http://apiUrl.com',
            'customerId' => '1',
        ];

        $this->guzzleMock->expects($this->once())
            ->method('setBaseUrl')
            ->will($this->returnValue('apiUrl'));

        $this->guzzleMock->expects($this->once())
            ->method('post')
            ->with(
                null,
                null,
                $this->requestData
            )
            ->will($this->returnValue($this->requestMock));

        $this->requestMock->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->responseMock));

        $this->responseMock->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue(['id' => 10]));

        $repository = $this->getMockBuilder('\Araneum\Bundle\AgentBundle\Repository\CustomerRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($customer));

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo('AraneumAgentBundle:Customer'))
            ->will($this->returnValue($repository));

        $this->em->expects($this->any())
            ->method('flush');

        $customer->expects($this->once())
            ->method('setSiteId')
            ->with($this->equalTo(10));

        $this->applicationApiSenderService->send(
            $this->requestData,
            $helper
        );
    }

    /**
     * Set Up
     */
    protected function setUp()
    {
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

        $this->responseMock = $this->getMock('\Guzzle\Http\Message\Response', ['getBody'], [200]);

        $this->applicationApiSenderService = new ApplicationApiSenderService($this->guzzleMock, $this->em, true);
    }
}
