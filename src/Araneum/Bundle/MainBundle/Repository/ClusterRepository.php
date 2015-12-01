<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Araneum\Bundle\MainBundle\Entity\Cluster;

/**
 * Class ClusterRepository for
 *
 * @package Araneum\Bundle\MainBundle\Repository
 */
class ClusterRepository extends EntityRepository
{
	/**
	 * Get statistic of all clusters average last 24 hours
	 *
	 * @return array
	 */
	public function getClusterLoadAverage()
	{
		$qb = $this->createQueryBuilder('c');

		$qb
			->select('c.name')
			->addSelect('DATE_PART(hour, l.createdAt) AS hours')
			->addSelect('SUM(l.averagePingTime)/count(l.averagePingTime) *100 AS apt')
			->leftJoin('AraneumAgentBundle:ConnectionLog', 'l', 'WITH', $qb->expr()->andX(
				$qb->expr()->eq('l.cluster', 'c'),
				$qb->expr()->neq('l.averagePingTime', -1),
				$qb->expr()->between('l.createdAt', ':start', ':end')
			))
			->groupBy('c.name, hours')
			->orderBy('c.name, hours')
			->setParameters(
				[
					'start' => date('Y-m-d H:i:s', time() - 86400),
					'end' => date('Y-m-d H:i:s', time())
				]
			)->setMaxResults(4);

		$result = $qb->getQuery()->getResult();
		return $result;
	}

	/**
	 * Get statistics of cluster statuses last 24 hours
	 *
	 * @return array
	 */
	public function getClusterUpTime()
	{
		$qb = $this->createQueryBuilder('c');

		$qb
			->select('c.name')
			->addSelect('ROUND(SUM(CAST(CASE WHEN cl.status = :success THEN 1 ELSE 0 END AS NUMERIC))/count(c.id), 2)*100 AS success')
			->addSelect('ROUND(SUM(CAST(CASE WHEN cl.status IN (:incorrect_application, :slow_connection, :unstable_connection) THEN 1 ELSE 0 END AS NUMERIC))/count(c.id), 2)*100 AS problem')
			->addSelect('ROUND(SUM(CAST(CASE WHEN cl.status = :offline THEN 1 ELSE 0 END AS NUMERIC))/count(c.id), 2)*100 AS offline')
			->leftJoin('AraneumAgentBundle:ClusterLog', 'cl', 'WITH', $qb->expr()->andX(
				$qb->expr()->eq('cl.cluster', 'c'),
				$qb->expr()->between('cl.createdAt', ':start', ':end')
			))
			->groupBy('c.name')
			->setParameters(
				[
					'success' => Cluster::STATUS_OK,
					'incorrect_application' => Cluster::STATUS_HAS_INCORRECT_APPLICATION,
					'slow_connection' => Cluster::STATUS_HAS_SLOW_CONNECTION,
					'unstable_connection' => Cluster::STATUS_HAS_UNSTABLE_CONNECTION,
					'offline' => Cluster::STATUS_OFFLINE,
					'start' => date('Y-m-d H:i:s', time() - 86400),
					'end' => date('Y-m-d H:i:s', time())
				]
			)
			->setMaxResults(4);

		$result = $qb->getQuery()->getResult();

		return $result;
	}
}