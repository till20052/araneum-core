<?php

namespace Araneum\Base\Tests\Unit\Service\Spot;

use Araneum\Base\Service\Spot\SpotApiSenderService;

/**
 * Class SpotApiSenderServiceTest
 *
 * @package Araneum\Base\Service\Spot
 */
class SpotApiSenderServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $guzzleMock;
    protected $spotApiSenderService;

    protected $requestData = [
        'key' => 'value',
        'key2' => 'value',
        'key3' => 'value',
    ];

    protected function setUp()
    {
        $this->guzzleMock = $this->getMockBuilder('\Guzzle\Service\ClientInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->spotApiSenderService = new SpotApiSenderService($this->guzzleMock, true);
    }

    /**
     *  Test normal work of method
     */
    public function testSendNormalData()
    {
        $spotCredential = [
            'url' => 'http://spotUrl.com',
            'userName' => 'spotUserName',
            'password' => 'spotPassword',
        ];
        $this->guzzleMock->expects($this->once())
            ->method('setBaseUrl')
            ->will($this->returnValue('spotUrl'));
        $this->guzzleMock->expects($this->once())
            ->method('post')
            ->with(
                null,
                null,
                $this->equalTo(
                    array_merge(
                        $this->requestData,
                        [
                            'api_username' => $spotCredential['userName'],
                            'api_password' => $spotCredential['password'],
                            'jsonResponse' => 'true',
                        ]
                    )
                )
            )
            ->will($this->returnValue('spotUrl'));

        $this->spotApiSenderService->send(
            $this->requestData,
            $spotCredential
        );
    }

    /**
     * Test method with bad spotOption data must throw exception
     *
     * @expectedException \BadMethodCallException
     */
    public function testSendBadDataException()
    {
        $spotCredential = [
            'url' => 'notValid',
            'userName' => 'spotUserName',
            'password' => 'spotPassword',
        ];

        $this->spotApiSenderService->send(
            $this->requestData,
            $spotCredential
        );
    }
}
