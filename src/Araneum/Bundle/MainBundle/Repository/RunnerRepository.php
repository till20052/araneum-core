<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Araneum\Bundle\MainBundle\Entity\Runner;
use Araneum\Base\Repository\CountableTrait;

/**
 * Class RunnerRepository
 *
 * @package Araneum\Bundle\MainBundle\Repository
 */
class RunnerRepository extends EntityRepository
{
    use CountableTrait;

    /**
     * Get statistic of all runners Average Ping Time for last 24h
     *
     * @return array
     */
    public function getRunnerLoadAverage()
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->select('r.name')
            ->addSelect('DATE_PART(hour, l.createdAt) AS hours')
            ->addSelect('SUM(l.averagePingTime)/count(l.averagePingTime) *100 AS apt')
            ->leftJoin(
                'AraneumAgentBundle:ConnectionLog',
                'l',
                'WITH',
                $qb->expr()->andX(
                    $qb->expr()->eq('l.runner', 'r'),
                    $qb->expr()->neq('l.averagePingTime', -1),
                    $qb->expr()->between('l.createdAt', ':start', ':end')
                )
            )
            ->groupBy('r.name, hours')
            ->orderBy('r.name, hours')
            ->setParameters(
                [
                    'start' => date('Y-m-d H:i:s', time() - 86400),
                    'end' => date('Y-m-d H:i:s', time()),
                ]
            )->setMaxResults(4);

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Get statistic of all runners Up Time for last 24h
     * @param int $maxResults
     * @return array
     */
    public function getRunnersUpTime($maxResults = 4)
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->select('r.name')
            ->addSelect('ROUND(SUM(CAST(CASE WHEN r.status = :success THEN 1 ELSE 0 END AS NUMERIC))/count(r.id), 2)*100 AS success')
            ->addSelect('ROUND(SUM(CAST(CASE WHEN r.status IN (:app_code_incorrect, :app_error) THEN 1 ELSE 0 END AS NUMERIC))/count(r.id), 2)*100 AS appProblem')
            ->addSelect('ROUND(SUM(CAST(CASE WHEN r.status IN (:code_incorrect, :error, :slow_connection, :unstable_connection) THEN 1 ELSE 0 END AS NUMERIC))/count(r.id), 2)*100 AS problem')
            ->addSelect('ROUND(SUM(CAST(CASE WHEN r.status = :offline THEN 1 ELSE 0 END AS NUMERIC))/count(r.id), 2)*100 AS offline')
            ->leftJoin(
                'AraneumAgentBundle:RunnerLog',
                'rl',
                'WITH',
                $qb->expr()->andX(
                    $qb->expr()->eq('rl.runner', 'r'),
                    $qb->expr()->between('rl.createdAt', ':start', ':end')
                )
            )
            ->groupBy('r.name')
            ->setParameters(
                [
                    'success' => Runner::STATUS_OK,
                    'code_incorrect' => Runner::STATUS_CODE_INCORRECT,
                    'error' => Runner::STATUS_ERROR,
                    'app_code_incorrect' => Runner::STATUS_APP_CODE_INCORRECT,
                    'app_error' => Runner::STATUS_APP_ERROR,
                    'slow_connection' => Runner::STATUS_HAS_SLOW_CONNECTION,
                    'unstable_connection' => Runner::STATUS_HAS_UNSTABLE_CONNECTION,
                    'offline' => Runner::STATUS_OFFLINE,
                    'start' => date('Y-m-d H:i:s', time() - 86400),
                    'end' => date('Y-m-d H:i:s', time()),
                ]
            )
            ->setMaxResults($maxResults);

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
