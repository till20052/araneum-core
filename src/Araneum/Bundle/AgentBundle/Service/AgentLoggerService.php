<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\ApplicationLog;
use Araneum\Bundle\AgentBundle\Entity\ClusterLog;
use Araneum\Bundle\AgentBundle\Entity\ConnectionLog;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Class AgentLoggerService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class AgentLoggerService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Service Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Log Connection
     *
     * @param Connection $connection
     * @param Cluster    $cluster
     * @param integer    $percentLostPackages
     * @param integer    $averagePingTime
     */
    public function logConnection(Connection $connection, Cluster $cluster, $percentLostPackages, $averagePingTime)
    {
        $log = (new ConnectionLog())
            ->setConnection($connection)
            ->setCluster($cluster)
            ->setPercentLostPackages($percentLostPackages)
            ->setAveragePingTime($averagePingTime);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    /**
     * Log Cluster
     *
     * @param Cluster         $cluster
     * @param integer         $status
     * @param ArrayCollection $problems
     */
    public function logCluster(Cluster $cluster, $status, ArrayCollection $problems)
    {
        $log = (new ClusterLog())
            ->setCluster($cluster)
            ->setStatus($status)
            ->setProblems($problems);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    /**
     * Log Application
     *
     * @param Application     $application
     * @param integer         $status
     * @param ArrayCollection $problems
     */
    public function logApplication(Application $application, $status, ArrayCollection $problems)
    {
        $log = (new ApplicationLog())
            ->setApplication($application)
            ->setStatus($status)
            ->setProblems($problems);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
