<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Service;

use Araneum\Bundle\AgentBundle\Entity\ApplicationLog;
use Araneum\Bundle\AgentBundle\Entity\ClusterLog;
use Araneum\Bundle\AgentBundle\Entity\ConnectionLog;
use Araneum\Bundle\AgentBundle\Service\AgentLoggerService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AgentLoggerServiceTest
 *
 * @package Araneum\Bundle\AgentBundle\Tests\Unit\Service
 */
class AgentLoggerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * @var AgentLoggerService
     */
    private $logger;

    /**
     * Test Log Connection
     */
    public function testLogConnection()
    {
        $connection = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $runner = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Runner')
            ->disableOriginalConstructor()
            ->getMock();

        $log = (new ConnectionLog())
            ->setConnection($connection)
            ->setRunner($runner)
            ->setPercentLostPackages(0)
            ->setAveragePingTime(0);

        $this->entityManager->expects($this->any())
            ->method('persist')
            ->with($this->equalTo($log));

        $this->logger->logConnection($connection, $runner, 0, 0);
    }

    /**
     * Test Log Connection
     */
    public function testLogApplication()
    {
        $application = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Application')
            ->disableOriginalConstructor()
            ->getMock();

        $log = (new ApplicationLog())
            ->setApplication($application)
            ->setStatus(Application::STATUS_OK)
            ->setProblems(new ArrayCollection());

        $this->entityManager->expects($this->any())
            ->method('persist')
            ->with($this->equalTo($log));

        $this->logger->logApplication($application, Application::STATUS_OK, new ArrayCollection());
    }

    /**
     * Test Log Connection
     */
    public function testLogCluster()
    {
        $cluster = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Entity\Cluster')
            ->disableOriginalConstructor()
            ->getMock();

        $log = (new ClusterLog())
            ->setCluster($cluster)
            ->setStatus(Cluster::STATUS_OK)
            ->setProblems(new ArrayCollection());

        $this->entityManager->expects($this->any())
            ->method('persist')
            ->with($this->equalTo($log));

        $this->logger->logCluster($cluster, Cluster::STATUS_OK, new ArrayCollection());
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->entityManager = $this->mockEntityManager();
        $this->logger = new AgentLoggerService($this->entityManager);
    }

    /**
     * Mock EntityManager
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockEntityManager()
    {
        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->any())
            ->method('flush');

        return $entityManager;
    }
}
