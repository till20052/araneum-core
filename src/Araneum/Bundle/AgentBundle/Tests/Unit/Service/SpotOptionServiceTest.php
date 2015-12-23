<?php

namespace Araneum\Bundle\AgentBundle\Test\Service;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\EntityManager;

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
     * @var
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

    protected function setUp()
    {
        $this->spotProducerServiceMock = $this->getMockBuilder('\Araneum\Base\Service\RabbitMQ\SpotProducerService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotApiSender = $this->getMockBuilder('\Araneum\Base\Service\Spot\SpotApiSenderService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->spotOptionService = new SpotOptionService(
            $this->spotProducerServiceMock,
            $this->spotApiSender,
            $this->entityManager
        );
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

    /**
     * Test get countries
     */
    public function testGetCountries()
    {

        $spotCredencials = [
            'url' => 'http:/\/\ultratrade.office.dev',
            'userName' => 'araneum',
            'password' => 'wU7tc2YKg2',
        ];

        $application = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Application')
            ->getMock();

        $application->expects($this->once())
            ->method('getSpotCredential')
            ->will(
                $this->returnValue(
                    $spotCredencials
                )
            );

        $repository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($repository));

        $repository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(['appKey' => 111111]))
            ->will($this->returnValue($application));

        $data = [
            'MODULE' => 'Country',
            'COMMAND' => 'view',
        ];

        $this->spotApiSender->expects($this->once())
            ->method('get')
            ->with($this->equalTo($data), $this->equalTo($spotCredencials))
            ->will($this->returnValue(true));

        $this->assertTrue($this->spotOptionService->getCountries(111111));
    }
}
