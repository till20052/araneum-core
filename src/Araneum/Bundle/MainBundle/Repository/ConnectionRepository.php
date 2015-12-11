<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Araneum\Base\Tests\Fixtures\Main\ConnectionFixtures;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * Class ConnectionRepository for
 *
 * @package Araneum\Bundle\MainBundle\Repository
 */
class ConnectionRepository extends EntityRepository implements \Countable
{
    /**
     * Return query with unused connections for clusters
     *
     * @param int $type
     * @return \Doctrine\ORM\Query
     */
    public function getQueryByUnusedAndType($type)
    {
        $qb = $this->createQueryBuilder('con');
        $qb
            ->leftJoin('AraneumMainBundle:Cluster', 'clu', Expr\Join::WITH, 'clu.host = con.id')
            ->where('con.type = :type')
            ->andWhere('con.enabled = true')
            ->andWhere('clu.id IS NULL')
            ->setParameters(['type' => $type]);

        return $qb->getQuery();
    }

    /**
     * Get Applications
     *
     * @param int $id
     * @return ArrayCollection
     */
    public function getApplications($id)
    {
        $q = $this->createQueryBuilder('cn')
            ->select('a')
            ->innerJoin('cn.clusters', 'cl')
            ->innerJoin('AraneumMainBundle:Application', 'a', Expr\Join::WITH, 'a.cluster = cl.id')
            ->where('cn.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return new ArrayCollection($q->getResult());
    }

    /**
     * get active host connections
     *
     * @param array $ids
     * @return array
     */
    public function getActiveHostConnections(array $ids)
    {
        $qb = $this->createQueryBuilder('con')
            ->where('con.type = :type')
            ->andWhere('con.enabled = true')
            ->andWhere('con.id IN (:ids)')
            ->setParameters(
                [
                    'type' => Connection::CONN_HOST,
                    'ids' => $ids,
                ]
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * Find Connection by appKey
     *
     * @param string $appKey
     * @return array
     */
    public function findConnectionByAppKey($appKey)
    {
        $qb = $this->createQueryBuilder('conn');

        $qb
            ->join('conn.clusters', 'clu')
            ->join('clu.applications', 'app')
            ->where('app.appKey = :appKey')
            ->setParameters(
                [
                    'appKey' => $appKey,
                ]
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * Get host by cluster Id
     *
     * @param int $clusterId
     * @return array
     */
    public function getHostByClusterId($clusterId)
    {
        $qb = $this->createQueryBuilder('conn');
        $qb
            ->join('conn.clusters', 'clu')
            ->where('clu.id = :clu')
            ->andWhere('conn.type = :type')
            ->andWhere('conn.enabled = true')
            ->setParameters(
                [
                    'clu' => $clusterId,
                    'type' => ConnectionFixtures::TEST_CONN_HOST_TYPE,
                ]
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return (int) $this->createQueryBuilder('conn')
                         ->select('COUNT(conn.id) as cnt')
                         ->getQuery()
                         ->getOneOrNullResult()['cnt'];
    }
}
