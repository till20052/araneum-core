<?php

namespace Araneum\Bundle\AgentBundle\Test\Unit\Service;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use Doctrine\ORM\EntityManager;

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
    protected $customerLoginProducerService;
    /**
     * @var SpotOptionService
     */
    protected $spotOptionService;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotProducerServiceMock;
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var
     */
    protected $spotApiSender;
    /**
     * Setup
     */
    protected function setUp()
    {
        $this->spotProducerServiceMock = $this
            ->getMockBuilder('\Araneum\Base\Service\RabbitMQ\SpotCustomerProducerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotApiSender = $this->getMockBuilder('\Araneum\Base\Service\Spot\SpotApiSenderService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerLoginProducerService = $this
            ->getMockBuilder('\Araneum\Base\Service\RabbitMQ\SpotCustomerLoginProducerService')
            ->disableOriginalConstructor()
            ->getMock();
        $this->spotOptionService = new SpotOptionService(
            $this->customerLoginProducerService,
            $this->spotProducerServiceMock,
            $this->spotApiSender,
            $this->entityManager
        );
    }

    /**
     * test customer reset password
     */
    public function testCustomerResetNormal()
    {
        $customer = (new Customer())
            ->setSpotId(123)
            ->setPassword('password');

        $customerData = [
            'MODULE' => 'Customer',
            'COMMAND' => 'edit',
            'customerId' => $customer->getSpotId(),
            'password' => $customer->getPassword(),
        ];

        $this->spotProducerServiceMock
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->equalTo($customerData),
                $this->equalTo($customer),
                $this->equalTo(CustomerLog::ACTION_RESET_PASSWORD)
            )
            ->will($this->returnValue(true));

        $this->assertTrue($this->spotOptionService->customerResetPassword($customer));
    }


    /**
     *  Test customer create
     */
    public function testCustomerCreateNormal()
    {
        $application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');
        $customer = (new Customer())
            ->setFirstName('firstName')
            ->setLastName('lastName')
            ->setCurrency('USD')
            ->setCountry(123)
            ->setEmail('test@mail.com')
            ->setPhone('123-3213-142412')
            ->setPassword('password')
            ->setApplication($application);

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

        $this->spotProducerServiceMock
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->equalTo($customerData),
                $this->equalTo($customer),
                $this->equalTo(CustomerLog::ACTION_CREATE)
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

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($repository));

        $this->spotApiSender->expects($this->once())
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

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($repository));

        $this->spotOptionService->getCountries($appKey);
    }
}
