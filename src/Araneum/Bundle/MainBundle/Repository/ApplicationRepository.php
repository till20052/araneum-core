<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\EntityRepository;

/**
 * ApplicationRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends EntityRepository
{
    /**
     * Get statistics of all applications by next conditions:
     *  - online
     *  - has problems
     *  - has errors
     *  - disabled
     *
     * return \stdClass
     */
    public function getApplicationsStatistics()
    {
        return (object) $this->createQueryBuilder('A')
            ->select('SUM(CASE WHEN A.enabled = TRUE AND A.status = :online THEN 1 ELSE 0 END) AS online')
            ->addSelect('SUM(CASE WHEN A.enabled = TRUE AND A.status = :hasProblem THEN 1 ELSE 0 END) as hasProblems')
            ->addSelect('SUM(CASE WHEN A.status > :hasProblem THEN 1 ELSE 0 END) as hasErrors')
            ->addSelect('SUM(CASE WHEN A.enabled = FALSE THEN 1 ELSE 0 END) as disabled')
            ->setParameters(
                [
                    'online' => Application::STATUS_OK,
                    'hasProblem' => Application::STATUS_CODE_INCORRECT
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get statistic of all application by last 24 hours
     *
     * @return array
     */
    public function getApplicationStatusesDayly()
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->select('a.name')
            ->addSelect('SUM(CASE WHEN l.status = 100 THEN 1 ELSE 0 END) / COUNT(a.id) * 100 AS errors')
            ->addSelect('SUM(CASE WHEN l.status = 1  THEN 1 ELSE 0 END) / COUNT(a.id) * 100 AS problems')
            ->addSelect('SUM(CASE WHEN l.status = 0  THEN 1 ELSE 0 END) / COUNT(a.id) * 100 AS success')
            ->addSelect('SUM(CASE WHEN l.status = 999 THEN 1 ELSE 0 END) / COUNT(a.id) * 100 AS disabled')
            ->leftJoin('AraneumAgentBundle:ApplicationLog', 'l', 'WITH', 'l.application = a')
            ->groupBy('a.name')
        ;

        $result = $qb->getQuery()->getResult();
        return $result;
    }

}