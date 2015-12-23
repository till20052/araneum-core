<?php

namespace Araneum\Bundle\AgentBundle\Test\Service;

use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Guzzle\Http\Message\Request;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $spotApiPublicUrlLogin = 'loginUrl';
    protected $application;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotApiSenderServiceMock;
    /**
     * @var SpotOptionService
     */
    protected $spotOptionService;

    /**
     * Test login method with normal data
     */
    public function testLoginNormal()
    {
        $customerId = 123;
        $session = 'd2925a4d5c856a6d09bc10c1f4f4ef51';

        $this->spotApiSenderServiceMock
            ->expects($this->once())
            ->method('sendToPublicUrl')
            ->with(
                $this->equalTo(Request::POST),
                $this->equalTo($this->spotApiPublicUrlLogin),
                $this->equalTo(
                    [
                        'email' => 'email',
                        'password' => 'password',
                    ]
                ),
                $this->equalTo($this->application)
            )
            ->will($this->returnValue($this->responseMock));

        $this->responseMock
            ->expects($this->any())
            ->method('json')
            ->will(
                $this->returnValue(
                    [
                        'status' => true,
                        'customerId' => $customerId,
                    ]
                )
            );

        $this->responseMock
            ->expects($this->once())
            ->method('getSetCookie')
            ->will(
                $this->returnValue(
                    'spotsession_1_2142478985='.$session.'; path=/; domain=.spotplatform.ultratrade.com'
                )
            );

        $this->assertEquals(
            [
                'spotsession' => $session,
                'customerId' => $customerId,
            ],
            $this->spotOptionService->login('email', 'password', $this->application)
        );
    }

    /**
     * Test login method with failed response
     */
    public function testLoginFail()
    {
        $this->spotApiSenderServiceMock
            ->expects($this->once())
            ->method('sendToPublicUrl')
            ->with(
                $this->equalTo(Request::POST),
                $this->equalTo($this->spotApiPublicUrlLogin),
                $this->equalTo(
                    [
                        'email' => 'email',
                        'password' => 'password',
                    ]
                ),
                $this->equalTo($this->application)
            )
            ->will($this->returnValue($this->responseMock));

        $this->responseMock
            ->expects($this->any())
            ->method('json')
            ->will(
                $this->returnValue(
                    [
                        'status' => false,
                    ]
                )
            );

        $this->assertFalse($this->spotOptionService->login('email', 'password', $this->application));
    }

    /**
     * Set Up
     */
    protected function setUp()
    {
        $this->spotApiSenderServiceMock = $this->getMockBuilder('\Araneum\Base\Service\Spot\SpotApiSenderService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder('\Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->application = new Application();

        $this->spotOptionService = new SpotOptionService($this->spotApiSenderServiceMock, $this->spotApiPublicUrlLogin);
    }
}
