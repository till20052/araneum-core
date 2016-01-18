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
     * @param $period string
     * @return int The custom count as an integer.
     */
    public function countForPeriod($period = '24H')
    {
        $date = new \DateTime();
        $date->add(\DateInterval::createFromDateString($period));
        $result = $this->createQueryBuilder('REPO')
            ->select('COUNT(REPO.id) as repoCount')
            ->where('REPO.createdAt BETWEEN :start AND :end')
            ->setParameters(
                [
                    'start' => $date->format('Y-m-d H:i:s'),
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
}
