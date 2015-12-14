<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

/**
 * ApplicationRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends EntityRepository implements \Countable
{
    /**
     * Get statistics of all applications by next conditions:
     *  - online
     *  - has problems
     *  - has errors
     *  - disabled
     *
     * @return \stdClass
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
                    'hasProblem' => Application::STATUS_CODE_INCORRECT,
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get statistic of all application by last 24 hours
     *
     * @param int $maxResults
     * @return array
     */
    public function getApplicationStatusesDayly($maxResults = 4)
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->select('a.name')
            ->addSelect(
                'ROUND(SUM(CAST(CASE WHEN l.status = :errors THEN 1 ELSE 0 END AS NUMERIC)) / COUNT(a.id), 2) * 100 AS errors'
            )
            ->addSelect(
                'ROUND(SUM(CAST(CASE WHEN l.status = :problems  THEN 1 ELSE 0 END AS NUMERIC)) / COUNT(a.id), 2) * 100 AS problems'
            )
            ->addSelect(
                'ROUND(SUM(CAST(CASE WHEN l.status = :success  THEN 1 ELSE 0 END AS NUMERIC)) / COUNT(a.id), 2) * 100 AS success'
            )
            ->addSelect(
                'ROUND(SUM(CAST(CASE WHEN l.status = :disabled THEN 1 ELSE 0 END AS NUMERIC)) / COUNT(a.id), 2) * 100 AS disabled'
            )
            ->leftJoin(
                'AraneumAgentBundle:ApplicationLog',
                'l',
                'WITH',
                $qb->expr()->andX(
                    $qb->expr()->eq('l.application', 'a'),
                    $qb->expr()->between('l.createdAt', ':start', ':end')
                )
            )
            ->groupBy('a.name')
            ->setParameters(
                [
                    'errors' => Application::STATUS_ERROR,
                    'problems' => Application::STATUS_CODE_INCORRECT,
                    'success' => Application::STATUS_OK,
                    'disabled' => Application::STATUS_DISABLED,
                    'start' => date('Y-m-d H:i:s', time() - 86400),
                    'end' => date('Y-m-d H:i:s', time()),
                ]
            )->setMaxResults($maxResults);

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return (int) $this->createQueryBuilder('a')
                         ->select('COUNT(a.id) as cnt')
                         ->getQuery()
                         ->getOneOrNullResult()['cnt'];
    }
}
