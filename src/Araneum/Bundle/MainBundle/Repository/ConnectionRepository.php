<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * Class ConnectionRepository for
 *
 * @package Araneum\Bundle\MainBundle\Repository
 */
class ConnectionRepository extends EntityRepository
{
    /**
     * Return query with unused connections for clusters
     *
     * @param $type
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
	 *
	 * @param $id
	 * @return array
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

		return $q->getResult();
	}
}