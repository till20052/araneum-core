<?php

namespace Araneum\Base\Tests\Unit\Service\Spot;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\RequestException;
use SebastianBergmann\GlobalState\Exception;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;

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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $em;

    /**
     * Test getErrors with normal data
     */
    public function testGetErrorsNormal()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('json')
            ->will(
                $this->returnValue(
                    [
                        'status' => [
                            'connection_status' => 'successful',
                            'operation_status' => 'successful',
                        ],
                    ]
                )
            );

        $this->assertNull($this->spotApiSenderService->getErrors($this->responseMock));
    }

    /**
     * Test getErrors with normal data
     */
    public function testGetErrorsBad()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('json')
            ->will(
                $this->returnValue(
                    [
                        'status' => [
                            'connection_status' => 'successful',
                            'operation_status' => 'fail',
                            'errors' => 'errors message',
                        ],
                    ]
                )
            );

        $this->assertEquals(json_encode('errors message'), $this->spotApiSenderService->getErrors($this->responseMock));
    }

    /**
     * Test getErrors with Exception
     *
     * @expectedException \BadMethodCallException
     */
    public function testGetErrorsException()
    {
        $this->responseMock
            ->expects($this->once())
            ->method('json')
            ->will($this->returnValue(['not valid response']));
        $this->spotApiSenderService->getErrors($this->responseMock);
    }

    /**
     *  Test normal work of method
     */
    public function testSendNormalData()
    {
        $this->requestMock->expects($this->once())->method('send');
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
            ->will($this->returnValue($this->requestMock));

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

        $result = $this->spotApiSenderService->send(
            $this->requestData,
            $spotCredential
        );
        if ($result instanceof \BadMethodCallException)
            throw new BadMethodCallException;
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

        $this->spotApiSenderService = new SpotApiSenderService($this->guzzleMock, $this->em, true);
    }
}
