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
     * @var array
     */
    private $applicationsDaylyStatistics;

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
        $this->applicationsStatistics = (object)
        [
            'online' => rand(),
            'hasProblems' => rand(),
            'hasErrors' => rand(),
            'disabled' => rand()
        ];

        $this->applicationsDaylyStatistics =
            [
                [
                    'name' => 'Name',
                    'errors' => 100,
                    'problems' => 0,
                    'success' => 0,
                    'disabled' => 0
                ]
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
     */
    public function testGetApplications()
    {
        $array = ['Name'];

        $this->assertEquals($array, $this->service->getResultByColumnName($this->applicationsDaylyStatistics, 'name'));
    }

    /**
     * Test get errors
     */
    public function testGetErrors()
    {
        $array =[100];

        $this->assertEquals($array, $this->service->getResultByColumnName($this->applicationsDaylyStatistics, 'errors'));
    }

    /**
     * Test get success
     */
    public function testGetSuccess()
    {
        $array = [0];

        $this->assertEquals($array, $this->service->getResultByColumnName($this->applicationsDaylyStatistics, 'success'));
    }

    /**
     * Test get problems
     */
    public function testGetProblems()
    {
        $array = [0];

        $this->assertEquals($array, $this->service->getResultByColumnName($this->applicationsDaylyStatistics, 'problems'));
    }

    /**
     * Test get disabled
     */
    public function testGetDisabled()
    {
        $array = [0];

        $this->assertEquals($array, $this->service->getResultByColumnName($this->applicationsDaylyStatistics, 'disabled'));
    }

}