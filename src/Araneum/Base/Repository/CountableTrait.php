<?php

namespace Araneum\Base\Repository;

trait CountableTrait
{
    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        $result = $this->createQueryBuilder('REPO')
            ->select('COUNT(REPO.id) as repoCount')
            ->where('REPO.created_at BETWEEN :start AND :end')
            ->getQuery()
            ->getOneOrNullResult();

        if (is_null($result)) {
            $result = ['repoCount' => 0];
        }

        return (int) $result['repoCount'];
    }

    /**
     * Count elements of an object for last 24h
     *
     * @return int The custom count as an integer.
     */
    public function countForLast24h()
    {
        $result = $this->createQueryBuilder('REPO')
            ->select('COUNT(REPO.id) as repoCount')
            ->where('REPO.created_at BETWEEN :start AND :end')
            ->setParameters(
                [
                    'start' => date('Y-m-d H:i:s', time() - 86400),
                    'end' => date('Y-m-d H:i:s', time()),
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();

        if (is_null($result)) {
            $result = ['repoCount' => 0];
        }

        return (int) $result['repoCount'];
    }

    /**
     * Count elements of an object for period
     * By default count elements for last 24h
     *
     * @param $startDate \DateTime
     * @param $endDate   \DateTime
     * @return int The custom count as an integer.
     */
    public function countForPeriod($startDate = null, $endDate = null)
    {
        if (is_null($startDate)) {
            $startDate = date('Y-m-d H:i:s', time() - 86400);
        }
        if (is_null($endDate)) {
            $endDate = date('Y-m-d H:i:s', time());
        }
        $result = $this->createQueryBuilder('REPO')
            ->select('COUNT(REPO.id) as repoCount')
            ->where('REPO.createdAt BETWEEN :start AND :end')
            ->setParameters(
                [
                    'start' => $startDate,
                    'end' => $endDate,
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();

        if (is_null($result)) {
            $result = ['repoCount' => 0];
        }

        return (int) $result['repoCount'];
    }
}
