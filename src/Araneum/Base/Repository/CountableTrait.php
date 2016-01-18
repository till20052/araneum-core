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
}
