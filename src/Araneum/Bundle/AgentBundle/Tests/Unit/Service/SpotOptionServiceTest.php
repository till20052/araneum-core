<?php

namespace Araneum\Bundle\AgentBundle\Test\Unit\Service;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\EntityManager;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use JMS\Serializer\Serializer;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotProducerServiceMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerLoginProducerService;
    /**
     * @var SpotOptionService
     */
    protected $spotOptionService;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotCustomerProducerServiceMock;
    /**
     * @var EntityManager
     */
    protected $entityManagerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotApiSenderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializer;

    /**
     * @var Application
     */
    protected $application;

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->spotCustomerProducerServiceMock = $this
            ->getMockBuilder('\Araneum\Base\Service\RabbitMQ\ProducerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotApiSenderMock = $this->getMockBuilder('\Araneum\Base\Service\Spot\SpotApiSenderService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerLoginProducerService = $this
            ->getMockBuilder('\Araneum\Base\Service\RabbitMQ\ProducerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotProducerServiceMock = $this->getMockBuilder('\Araneum\Base\Service\RabbitMQ\ProducerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializer = $this->getMockBuilder('\JMS\Serializer\Serializer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->application = (new Application())
            ->setSpotApiUrl('url')
            ->setSpotApiUser('user')
            ->setSpotApiPassword('password')
            ->setSpotApiPublicUrl('publicUrl')
        ;

        $this->spotOptionService = new SpotOptionService(
            $this->spotCustomerProducerServiceMock,
            $this->customerLoginProducerService,
            $this->spotProducerServiceMock,
            $this->spotApiSenderMock,
            $this->entityManagerMock,
            $this->serializer
        );
    }

    /**
     * test customer reset password
     */
    public function testCustomerResetNormal()
    {

        $customer = (new Customer())
            ->setSpotId(123)
            ->setPassword('password')
            ->setApplication($this->application)
        ;

        $customerData = [
            'MODULE' => 'Customer',
            'COMMAND' => 'edit',
            'customerId' => $customer->getSpotId(),
            'password' => $customer->getPassword(),
        ];

        $credentials = [
            'spotCredential' => $this->application->getSpotCredential(),
            'log' => [
                'action' => CustomerLog::ACTION_RESET_PASSWORD,
                'customerId' => $customer->getId(),
                'applicationId' => $this->application->getId(),
            ],
        ];

        $this->spotCustomerProducerServiceMock
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->equalTo($customerData),
                $this->equalTo($credentials)
            )
            ->will($this->returnValue(true));

        $this->assertTrue($this->spotOptionService->customerResetPassword($customer));
    }

    /**
     *  Test customer create
     */
    public function testCustomerCreateNormal()
    {
        $customer = (new Customer())
            ->setFirstName('firstName')
            ->setLastName('lastName')
            ->setCurrency('USD')
            ->setCountry(123)
            ->setEmail('test@mail.com')
            ->setPhone('123-3213-142412')
            ->setPassword('password')
            ->setApplication($this->application);

        $customerData = [
            'MODULE' => 'Customer',
            'COMMAND' => 'add',
            'FirstName' => $customer->getFirstName(),
            'LastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'password' => $customer->getPassword(),
            'Phone' => $customer->getPhone(),
            'Country' => $customer->getCountry(),
            'currency' => $customer->getCurrency(),
        ];

        $credentials = [
            'spotCredential' => $this->application->getSpotCredential(),
            'log' => [
                'action' => CustomerLog::ACTION_CREATE,
                'customerId' => $customer->getId(),
                'applicationId' => $this->application->getId(),
            ],
        ];

        $this->spotCustomerProducerServiceMock
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->equalTo($customerData),
                $this->equalTo($credentials)
            )
            ->will($this->returnValue(true));

        $this->assertTrue($this->spotOptionService->customerCreate($customer));
    }

    /**
     * Test get countries
     */
    public function testGetCountries()
    {
        $appKey = md5(microtime(true));
        $data = [
            'MODULE' => 'Country',
            'COMMAND' => 'view',
        ];
        $spotCredentials = [
            'url' => 'http:/\/\ultratrade.office.dev',
            'userName' => 'araneum',
            'password' => 'wU7tc2YKg2',
        ];

        $application = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Application')
            ->disableOriginalConstructor()
            ->getMock();

        $application->expects($this->once())
            ->method('getSpotCredential')
            ->will($this->returnValue($spotCredentials));

        $repository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findOneByAppKey'])
            ->getMock();

        $repository->expects($this->once())
            ->method('findOneByAppKey')
            ->with($this->equalTo($appKey))
            ->will($this->returnValue($application));

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($repository));

        $this->spotApiSenderMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo($data), $this->equalTo($spotCredentials))
            ->will($this->returnValue(true));

        $this->assertTrue($this->spotOptionService->getCountries($appKey));
    }

    /**
     * Test SpotOptionService.getCountries(...) in case if return NotFoundHttpException
     *
     * @expectedException           \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionCode       \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
     * @expectedExceptionMessage    Not Application found for this appKey
     */
    public function testGetCountriesEntityNotFoundException()
    {
        $appKey = md5(microtime(true));

        $repository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findOneByAppKey'])
            ->getMock();
        $repository->expects($this->once())
            ->method('findOneByAppKey')
            ->with($this->equalTo($appKey))
            ->will($this->returnValue(null));

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($repository));

        $this->spotOptionService->getCountries($appKey);
    }

    /**
     * Test leadCreate method
     */
    public function testLeadCreate()
    {
        $application = (new Application())
            ->setSpotApiUrl('http://test.com')
            ->setSpotApiUser('user')
            ->setSpotApiPassword('password');

        $lead = (new Lead())
            ->setFirstName('firstName')
            ->setLastName('lastName')
            ->setCountry(123)
            ->setEmail('test@mail.com')
            ->setPhone('123-3213-142412')
            ->setApplication($application);

        $customerData = [
            'MODULE' => 'Lead',
            'COMMAND' => 'add',
            'FirstName' => $lead->getFirstName(),
            'LastName' => $lead->getLastName(),
            'Phone' => $lead->getPhone(),
            'Country' => $lead->getCountry(),
            'email' => $lead->getEmail(),
        ];

        $this->spotProducerServiceMock
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->equalTo($customerData),
                $this->equalTo(
                    [
                        'url' => 'http://test.com',
                        'userName' => 'user',
                        'password' => 'password',
                    ]
                )
            )
            ->will($this->returnValue(true));

        $this->assertTrue($this->spotOptionService->leadCreate($lead));
    }
}
