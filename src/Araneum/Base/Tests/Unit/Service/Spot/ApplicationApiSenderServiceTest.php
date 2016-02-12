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
    public function testSendNormalData()
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

        $this->guzzleMock->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->responseMock));

        $this->responseMock->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(['error'=>'123']));

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
