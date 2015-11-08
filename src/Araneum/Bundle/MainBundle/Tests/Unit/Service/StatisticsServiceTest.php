<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Bundle\MainBundle\Service\StatisticsService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StatisticsServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatisticsService
     */
    private $service;

    /**
     * @var \stdClass
     */
    private $applicationsStatistics;

    /**
     * Mock instance of Container
     *
     * @return ContainerInterface
     */
    private function container()
    {
        /** @var  $container */
        $container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $applicationRepository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ApplicationRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $applicationRepository->expects($this->once())
            ->method('getApplicationsStatistics')
            ->will($this->returnValue($this->applicationsStatistics));

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($applicationRepository));

        $container->expects($this->any())
            ->method('get')
            ->with($this->logicalOr(
                $this->equalTo('doctrine.orm.entity_manager')
            ))
            ->will($this->returnCallback(function ($object) use ($entityManager) {
                if ($object == 'doctrine.orm.entity_manager') {
                    return $entityManager;
                }
            }));

        return $container;
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->applicationsStatistics = (object) [
            'online' => rand(),
            'hasProblems' => rand(),
            'hasErrors' => rand(),
            'disabled' => rand()
        ];

        $this->service = new StatisticsService($this->container());
    }

    /**
     * Test getting statistics of each application
     */
    public function testGetApplicationsStatistics()
    {
        $this->assertEquals(
            $this->applicationsStatistics,
            $this->service->getApplicationsStatistics()
        );
    }
}