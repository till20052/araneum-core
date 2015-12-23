<?php

namespace Araneum\Base\Tests\Unit\Service\Spot;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Guzzle\Http\Message\Request;

/**
 * Class SpotApiSenderServiceTest
 *
 * @package Araneum\Base\Service\Spot
 */
class SpotApiSenderServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $guzzleMock;
    protected $application;
    /**
     * @var SpotApiSenderService
     */
    protected $spotApiSenderService;
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
     *  Test normal work of method
     */
    public function testSendToPublicNormalData()
    {
        $requestData = ['key' => 'value'];
        $resourcePath = 'path';
        $this->guzzleMock
            ->expects($this->once())
            ->method('setBaseUrl')
            ->with($this->equalTo('https://test.com'));

        $this->guzzleMock
            ->expects($this->once())
            ->method('createRequest')
            ->with(
                $this->equalTo(Request::POST),
                $this->equalTo($resourcePath),
                $this->equalTo(null),
                $this->equalTo($requestData)
            )
            ->will($this->returnValue($this->responseMock));

        $this->spotApiSenderService->sendToPublicUrl(Request::POST, $resourcePath, $requestData, $this->application);
    }

    /**
     * Test method with bad spotOption data must throw exception
     *
     * @expectedException \BadMethodCallException
     */
    public function testSendToPublicBadData()
    {
        $this->application->setSpotApiPublicUrl('notvalid');
        $this->spotApiSenderService->sendToPublicUrl(Request::POST, 'path', ['key' => 'value'], $this->application);
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
            ->setMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->application = (new Application())->setSpotApiPublicUrl('https://test.com');
        $this->spotApiSenderService = new SpotApiSenderService($this->guzzleMock);
    }
}
