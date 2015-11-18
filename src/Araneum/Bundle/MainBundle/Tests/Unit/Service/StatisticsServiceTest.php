<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Bundle\MainBundle\Service\StatisticsService;
use Doctrine\ORM\EntityManager;

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
     * Mock instance of EntityManager
     *
     * @return EntityManager
     */
    private function entityManager()
    {
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

        return $entityManager;
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

        $this->service = new StatisticsService($this->entityManager());
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