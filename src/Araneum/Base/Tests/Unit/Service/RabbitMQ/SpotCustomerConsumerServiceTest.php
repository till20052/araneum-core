<?php

namespace Araneum\Base\Tests\Unit\Service\RabbitMQ;

use Araneum\Base\Service\RabbitMQ\SpotCustomerConsumerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\MainBundle\Entity\Application;
use Guzzle\Http\Exception\RequestException;

/**
 * Class SpotCustomerConsumerServiceTest
 *
 * @package Araneum\Base\Service\RabbitMQ
 */
class SpotCustomerConsumerServiceTest extends \PHPUnit_Framework_TestCase
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
    protected static $data           = [
        'key' => 'value',
        'key2' => 'value',
        'key3' => 'value',
    ];
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotApiSenderServiceMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $msgConvertHelperMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $emMock;
    /**
     * @var SpotCustomerConsumerService
     */
    protected $spotCustomerConsumerService;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $amqMessageMock;
    /**
     * @var
     */
    private $msg;
    /**
     * @var
     */
    private $encodeMsg;

    /**
     * Test execute method with normal data
     */
    public function testExecuteNormal()
    {
        $response = $this->getMockBuilder('\Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();

        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('bodyString'));

        $this->spotApiSenderServiceMock
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo(self::$data), $this->equalTo(self::$spotCredential))
            ->will($this->returnValue($response));

        $this->spotApiSenderServiceMock
            ->expects($this->once())
            ->method('getErrors')
            ->with($this->equalTo($response))
            ->will($this->returnValue(null));

        $this->createCustomerLogMock('bodyString', CustomerLog::STATUS_OK);

        $this->spotCustomerConsumerService->execute($this->amqMessageMock);
    }


    /**
     * Test execute method with Bad spot Response
     */
    public function testExecuteBad()
    {
        $response = $this->getMockBuilder('\Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();

        $this->spotApiSenderServiceMock
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo(self::$data), $this->equalTo(self::$spotCredential))
            ->will($this->returnValue($response));

        $this->spotApiSenderServiceMock
            ->expects($this->exactly(2))
            ->method('getErrors')
            ->with($this->equalTo($response))
            ->will($this->returnValue('error message'));

        $this->createCustomerLogMock('error message', CustomerLog::STATUS_ERROR);

        $this->spotCustomerConsumerService->execute($this->amqMessageMock);
    }


    /**
     * Test execute method with Exception in send method
     */
    public function testExecuteException()
    {
        $this->spotApiSenderServiceMock
            ->expects($this->once())
            ->method('send')
            ->will($this->throwException(new RequestException('error message')));

        $this->createCustomerLogMock('error message', CustomerLog::STATUS_ERROR);

        $this->spotCustomerConsumerService->execute($this->amqMessageMock);
    }


    /**
     * Set Up
     */
    protected function setUp()
    {
        $this->spotApiSenderServiceMock = $this
            ->getMockBuilder('\Araneum\Base\Service\Spot\SpotApiSenderService')
            ->disableOriginalConstructor()
            ->getMock();
        $this->msgConvertHelperMock = $this
            ->getMockBuilder('\Araneum\Base\Service\RabbitMQ\MessageConversionHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->emMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $this->msg = self::getMessageObject();
        $this->encodeMsg = serialize(json_encode($this->msg));
        $this->msgConvertHelperMock->expects($this->once())
            ->method('decodeMsg')
            ->with($this->equalTo($this->encodeMsg))
            ->will($this->returnValue($this->msg));

        $this->amqMessageMock = $this->getMock('\PhpAmqpLib\Message\AMQPMessage');
        $this->amqMessageMock->body = $this->encodeMsg;

        $this->spotCustomerConsumerService = new SpotCustomerConsumerService(
            $this->spotApiSenderServiceMock,
            $this->msgConvertHelperMock,
            $this->emMock
        );
    }

    /**
     * Get message object
     *
     * @return object $msg
     */
    private static function getMessageObject()
    {
        $msg = new \stdClass();
        $msg->spotCredential = self::$spotCredential;
        $msg->log = self::$log;
        $msg->data = self::$data;

        return $msg;
    }

    /**
     * @param $spotResponse
     * @param $status
     */
    private function createCustomerLogMock($spotResponse, $status)
    {
        $application = new Application();
        $this->emMock->expects($this->at(0))
            ->method('getReference')
            ->with('AraneumMainBundle:Application', self::$log['applicationId'])
            ->will($this->returnValue($application));

        $this->emMock->expects($this->at(1))->method('getReference')
            ->with('AraneumAgentBundle:Customer', self::$log['customerId'])
            ->will($this->returnValue(new Customer()));

        $customerLog = (new CustomerLog())
            ->setAction(self::$log['action'])
            ->setApplication($application)
            ->setCustomer(new Customer())
            ->setSpotResponse($spotResponse)
            ->setStatus($status);

        $this->emMock->expects($this->once())->method('persist')->with(
            $this->equalTo($customerLog)
        );
        $this->emMock->expects($this->once())->method('flush');
    }
}
