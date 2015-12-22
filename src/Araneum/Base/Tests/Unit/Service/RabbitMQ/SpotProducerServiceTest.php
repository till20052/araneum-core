<?php

namespace Araneum\Base\Tests\Unit\Service\RabbitMQ;

use Araneum\Base\Service\RabbitMQ\ProducerService;
use Araneum\Base\Service\RabbitMQ\SpotProducerService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ProducerServiceTest
 *
 * @package Araneum\Base\Tests\Unit\Service\RabbitMQ
 */
class ProducerServiceTest extends \PHPUnit_Framework_TestCase
{
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
     * @var
     */
    private $producerService;

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

        $this->assertTrue($this->producerService->publish($this->msg, $this->application));
    }

    /**
     * Test with Exception in publish method
     */
    public function testPublishWithException()
    {
        $this->producerMock->expects($this->once())
            ->method('publish')
            ->will($this->throwException(new Exception('Exception message')));

        $this->assertEquals('Exception message', $this->producerService->publish($this->msg, $this->application));
    }

    /**
     * Get message object
     *
     * @return object $msg
     */
    public static function getMessageObject()
    {
        $msg = new \stdClass();
        $msg->spotCredential = [
            'url' => 'spotUrl',
            'userName' => 'spotUserName',
            'password' => 'spotPassword',
        ];
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
        $this->application = new Application();
        $this->application->setSpotApiUrl('spotUrl');
        $this->application->setSpotApiUser('spotUserName');
        $this->application->setSpotApiPassword('spotPassword');
        $this->producerService = new SpotProducerService(
            $this->producerMock,
            $this->msgConvertHelperMock,
            $this->expirationMock
        );
    }
}
