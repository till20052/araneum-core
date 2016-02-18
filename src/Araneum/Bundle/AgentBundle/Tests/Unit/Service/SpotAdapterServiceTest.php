<?php

namespace Araneum\Bundle\AgentBundle\Test\Unit\Service;

use Araneum\Bundle\AgentBundle\Service\SpotAdapterService;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;

/**
 * Class SpotAdapterServiceTest
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotAdapterServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SpotAdapterService
     */
    protected $spotAdapterService;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotProducerServiceMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityManagerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $applicationManagerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $application;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotApiSenderServiceMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    const APP_KEY = ApplicationFixtures::TEST_APP_APP_KEY;

    const POST_DATA = [
        'normal_post_data_not_fully' => [
            'appKey' => ApplicationFixtures::TEST_APP_APP_KEY,
            'MODULE' => 'Not Empty',
            'COMMAND' => 'Not Empty',
        ],
        'normal_post_data_fully' => [
            'appKey' => ApplicationFixtures::TEST_APP_APP_KEY,
            'MODULE' => 'Not Empty',
            'COMMAND' => 'Not Empty',
            'requestData' => '{"1":"foo","2":"bar","3":"baz","4":"blong"}',
        ],
        'normal_post_data_fully_rabbit' => [
            'appKey' => ApplicationFixtures::TEST_APP_APP_KEY,
            'MODULE' => 'Not Empty',
            'COMMAND' => 'Not Empty',
            'requestData' => '{"1":"foo","2":"bar","3":"baz","4":"blong"}',
            'guaranteeDelivery' => true,
        ],
    ];

    const SPOT_CREDENTIAL_TEST = [
        'url' => 'url',
        'userName' => 'userName',
        'password' => 'password',
    ];

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotApiSenderServiceMock = $this->getMockBuilder('\Araneum\Base\Service\Spot\SpotApiSenderService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotProducerServiceMock = $this->getMockBuilder('\Araneum\Base\Service\RabbitMQ\SpotProducerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->applicationManagerMock = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findOneByAppKey'])
            ->getMock();

        $this->application = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Application')
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder('\Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($this->applicationManagerMock));


        $this->spotAdapterService = new SpotAdapterService(
            $this->entityManagerMock,
            $this->spotApiSenderServiceMock,
            $this->spotProducerServiceMock
        );
    }

    /**
     * Mock Services
     */
    protected function mockServices()
    {
        $this->application->expects($this->once())
            ->method('getSpotCredential')
            ->will($this->returnValue(self::SPOT_CREDENTIAL_TEST));

        $this->applicationManagerMock->expects($this->once())
            ->method('findOneByAppKey')
            ->will($this->returnValue($this->application));

        $this->spotApiSenderServiceMock->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->responseMock));

        $this->responseMock->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('Response'));
    }

    /**
     * Test SpotAdapterService Bad data with Bad Request
     */
    public function testNormalDataNotFully()
    {
        $data = self::POST_DATA['normal_post_data_not_fully'];

        $this->mockServices();

        $this->spotAdapterService->sendRequestToSpot($data);
    }

    /**
     * Test SpotAdapterService Normal data without Rabbit
     */
    public function testNormalDataFully()
    {
        $data = self::POST_DATA['normal_post_data_fully'];

        $this->mockServices();

        $this->spotAdapterService->sendRequestToSpot($data);
    }

    /**
     * Test SpotAdapterService Normal data and Rabbit response
     */
    public function testNormalDataFullyRabbit()
    {
        $data = self::POST_DATA['normal_post_data_fully_rabbit'];

        $this->application->expects($this->once())
            ->method('getSpotCredential')
            ->will($this->returnValue(self::SPOT_CREDENTIAL_TEST));

        $this->applicationManagerMock->expects($this->once())
            ->method('findOneByAppKey')
            ->will($this->returnValue($this->application));

        $this->spotProducerServiceMock->expects($this->once())
            ->method('publish')
            ->will($this->returnValue(true));

        $this->spotAdapterService->sendRequestToSpot($data);
    }
}
