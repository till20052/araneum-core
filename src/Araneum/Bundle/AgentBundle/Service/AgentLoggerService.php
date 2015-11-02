<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\ClusterLog;
use Araneum\Bundle\AgentBundle\Entity\ConnectionLog;
use Araneum\Bundle\AgentBundle\Entity\Problem;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\ORM\EntityManager;

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
	 * Log connection
	 *
	 * @param Connection $connection
	 * @param Cluster $cluster
	 * @param integer $percentLostPackages
	 * @param integer $averagePingTime
	 */
	public function logConnection(Connection $connection, Cluster $cluster, $percentLostPackages, $averagePingTime)
	{
		/** @var ConnectionLog $connectionLog */
		$log = (new ConnectionLog())
			->setConnection($connection)
			->setCluster($cluster)
			->setPercentLostPackages($percentLostPackages)
			->setAveragePingTime($averagePingTime);

		$this->entityManager->persist($log);
		$this->entityManager->flush();
	}

	public function logCluster($cluster, $status, $description)
	{
		$problem = (new Problem())
			->setStatus($status)
			->setDescription($description);

		$log = (new ClusterLog())
			->set;


		$this->entityManager->flush();
	}

	public function logApplication($application, $status, $description)
	{

	}
}