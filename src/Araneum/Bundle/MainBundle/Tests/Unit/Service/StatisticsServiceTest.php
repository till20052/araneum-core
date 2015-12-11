<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Bundle\MainBundle\Service\StatisticsService;
use Doctrine\ORM\EntityManager;

/**
 * Class StatisticsServiceTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service
 */
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
     * @var array
     */
    private $applicationsDaylyStatistics;

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

    /**
     * Test application statistics dayly
     */
    public function testGetApplicationsStatusesDayly()
    {
        $this->assertEquals(
            $this->applicationsDaylyStatistics,
            $this->service->getApplicationsStatusesDayly()
        );
    }

    /**
     * Test get Applications
     *
     * @dataProvider dataFields
     * @param array  $expected
     * @param string $field
     */
    public function testGetFields(array $expected, $field)
    {
        $this->assertEquals(
            $expected,
            $this->service->getResultByColumnName($this->applicationsDaylyStatistics, $field)
        );
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function dataFields()
    {
        return [
            'Test appName' =>
                [
                    ['Name'],
                    'name',
                ],
            'Test errors' =>
                [
                    [100],
                    'errors',
                ],
            'Test success' =>
                [
                    [0],
                    'success',
                ],
            'Test problems' =>
                [
                    [0],
                    'problems',
                ],
            'Test disabled' =>
                [
                    [0],
                    'disabled',
                ],
        ];
    }

    /**
     * Test get application statuses dayly
     */
    public function testGetApplicationStatusesDayly()
    {
        $array = [
            'name' => '',
            'errors' => '',
            'problems' => '',
            'success' => '',
            'disabled' => '',
        ];

        $statuses = $this->service->getApplicationsStatusesDayly();

        $this->assertEquals(array_keys($array), array_keys($statuses[0]));
    }

    /**
     * Test get average application statuses dayly
     */
    public function testGetAverageApplicationStatusesDayly()
    {

        $array = [
            'hours' => '',
            'errors' => '',
            'problems' => '',
            'success' => '',
            'disabled' => '',
        ];

        $applicationLogRepository = $this->getMockBuilder(
            '\Araneum\Bundle\AgentBundle\Repository\ApplicationLogRepository'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $applicationLogRepository->expects($this->once())
            ->method('getAverageApplicationStatusesDayly')
            ->will($this->returnValue($array));

        $entityManagerLog = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerLog->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumAgentBundle:ApplicationLog'))
            ->will($this->returnValue($applicationLogRepository));

        $service = new StatisticsService($entityManagerLog);

        $statuses = $service->getAverageApplicationStatusesDayly();

        $this->assertEquals(array_keys($array), array_keys($statuses));
    }

    /**
     * Test prepare result for cluster average
     */
    public function testPrepareResultForClusterAverage()
    {

        $array = [
            [
                'name' => 'name',
                'hours' => '10',
                'apt' => '1',
            ],
        ];

        $clusterRepository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ClusterRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $clusterRepository->expects($this->once())
            ->method('getClusterLoadAverage')
            ->will($this->returnValue($array));

        $entityManagerLog = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManagerLog->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Cluster'))
            ->will($this->returnValue($clusterRepository));

        $service = new StatisticsService($entityManagerLog);

        $clusterAverage = $service->prepareResultForClusterAverage();

        $this->assertEquals(array_keys($array), array_keys($clusterAverage));
    }

    /**
     * Test for
     */
    public function testPrepareResultForClusterUpTime()
    {

        $array = [
            [
                'label' => '',
                'color' => '',
                'data' => [],
            ],
            [
                'label' => '',
                'color' => '',
                'data' => [],
            ],
            [
                'label' => '',
                'color' => '',
                'data' => [],
            ],
        ];

        $arrayForCluster = [
            [
                'name' => '',
                'success' => '',
                'problem' => '',
                'offline' => '',
            ],
        ];

        $clusterRepository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ClusterRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $clusterRepository->expects($this->once())
            ->method('getClusterUpTime')
            ->will($this->returnValue($arrayForCluster));

        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Cluster'))
            ->will($this->returnValue($clusterRepository));

        $service = new StatisticsService($entityManager);

        $clusterUpTime = $service->prepareResultForClusterUpTime();

        $this->assertEquals(array_keys($array), array_keys($clusterUpTime));
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
            'disabled' => rand(),
        ];

        $this->applicationsDaylyStatistics =
            [
                [
                    'name' => 'Name',
                    'errors' => 100,
                    'problems' => 0,
                    'success' => 0,
                    'disabled' => 0,
                ],
            ];

        $this->service = new StatisticsService($this->entityManager());
    }

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

        $applicationRepository->expects($this->any())
            ->method('getApplicationsStatistics')
            ->will($this->returnValue($this->applicationsStatistics));

        $applicationRepository->expects($this->any())
            ->method('getApplicationStatusesDayly')
            ->will($this->returnValue($this->applicationsDaylyStatistics));

        $entityManager->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($applicationRepository));

        return $entityManager;
    }
}
