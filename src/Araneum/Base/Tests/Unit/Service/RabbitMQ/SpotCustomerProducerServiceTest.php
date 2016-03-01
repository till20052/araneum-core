<?php

namespace Araneum\Base\Tests\Unit\Service\RabbitMQ;

use Araneum\Base\Service\RabbitMQ\SpotCustomerProducerService;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ProducerServiceTest
 *
 * @package Araneum\Base\Tests\Unit\Service\RabbitMQ
 */
class SpotCustomerProducerServiceTest extends \PHPUnit_Framework_TestCase
{
    protected static $spotCredential = [
        'url' => 'spotUrl',
        'userName' => 'spotUserName',
        'password' => 'spotPassword',
    ];
    protected static $log            = [
        'action' => 'actionName',
        'customerId' => 123,
        'applicationId' => 321,
    ];
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customer;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $application;
    /**
     * @var
     */
    private $producerMock;

    /**
     * @var
     */
    private $msgConvertHelperMock;

    /**
     * @var
     */
    private $expirationMock = 3600000000;

    /**
     * @var SpotCustomerProducerService
     */
    private $customerProducerService;

    /**
     * @var
     */
    private $msg;

    /**
     * @var
     */
    private $encodeMsg;

    /**
     * Test with correct msg
     */
    public function testPublishWithCorrectMsg()
    {
        $this->producerMock->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($this->encodeMsg));

        $this->assertTrue($this->customerProducerService->publish($this->msg, $this->customer, self::$log['action']));
    }

    /**
     * Test with Exception in publish method
     */
    public function testPublishWithException()
    {
        $this->producerMock->expects($this->once())
            ->method('publish')
            ->will($this->throwException(new Exception('Exception message')));

        $result = $this->customerProducerService->publish($this->msg, $this->customer, self::$log['action']);
        $this->assertEquals('Exception message', $result);
    }

    /**
     * Get message object
     *
     * @return object $msg
     */
    public static function getMessageObject()
    {
        $msg = new \stdClass();
        $msg->spotCredential = self::$spotCredential;
        $msg->log = self::$log;
        $msg->data = [
            'key' => 'value',
            'key2' => 'value',
            'key3' => 'value',
        ];

        return $msg;
    }

    /**
     * Method execute before tests
     */
    protected function setUp()
    {
        $this->producerMock = $this
            ->getMockBuilder('\OldSound\RabbitMqBundle\RabbitMq\Producer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->msgConvertHelperMock = $this
            ->getMockBuilder('\Araneum\Base\Service\RabbitMQ\MessageConversionHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->msg = self::getMessageObject();
        $this->encodeMsg = serialize(json_encode($this->msg));
        $this->msgConvertHelperMock->expects($this->once())
            ->method('encodeMsg')
            ->with($this->isInstanceOf($this->msg))
            ->will($this->returnValue($this->encodeMsg));

        $this->application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');
        $this->application->expects($this->any())
            ->method('getId')->will($this->returnValue(self::$log['applicationId']));
        $this->application->expects($this->any())
            ->method('getSpotApiUrl')->will($this->returnValue(self::$spotCredential['url']));
        $this->application->expects($this->any())
            ->method('getSpotApiUser')->will($this->returnValue(self::$spotCredential['userName']));
        $this->application->expects($this->any())
            ->method('getSpotApiPassword')->will($this->returnValue(self::$spotCredential['password']));

        $this->customer = $this->getMock('\Araneum\Bundle\AgentBundle\Entity\Customer');
        $this->customer->expects($this->any())->method('getId')->will($this->returnValue(self::$log['customerId']));
        $this->customer->expects($this->any())->method('getApplication')->will($this->returnValue($this->application));

        $this->customerProducerService = new SpotCustomerProducerService(
            $this->producerMock,
            $this->msgConvertHelperMock,
            $this->expirationMock
        );
    }
}
