<?php

namespace Araneum\Bundle\AgentBundle\Test\Service;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SpotOptionService
     */
    protected $spotOptionService;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spotProducerServiceMock;

    protected function setUp()
    {
        $this->spotProducerServiceMock = $this->getMockBuilder('\Araneum\Base\Service\RabbitMQ\SpotProducerService')
            ->disableOriginalConstructor()
            ->getMock();


        $this->spotOptionService = new SpotOptionService($this->spotProducerServiceMock);
    }

    /**
     *  Test
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
                $this->equalTo($application)
            )
            ->will($this->returnValue(true));

        $this->assertTrue($this->spotOptionService->customerCreate($customer));
    }
}
