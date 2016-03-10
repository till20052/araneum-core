<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\ApplicationLog;
use Araneum\Bundle\AgentBundle\Entity\ClusterLog;
use Araneum\Bundle\AgentBundle\Entity\ConnectionLog;
use Araneum\Bundle\AgentBundle\Entity\RunnerLog;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Runner;
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
     * @param Runner     $runner
     * @param integer    $percentLostPackages
     * @param integer    $averagePingTime
     */
    public function logConnection(Connection $connection, Runner $runner, $percentLostPackages, $averagePingTime)
    {
        $log = (new ConnectionLog())
            ->setConnection($connection)
            ->setRunner($runner)
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
            ->setProblems($problems); //possible use addProblem instead of set

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    /**
     * Log Runner
     *
     * @param Runner  $runner
     * @param Cluster $cluster
     * @param integer $status
     */
    public function logRunner(Runner $runner, Cluster $cluster, $status)
    {
        $log = (new RunnerLog())
            ->setRunner($runner)
            ->setCluster($cluster)
            ->setStatus($status);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
