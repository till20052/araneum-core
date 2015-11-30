<?php

namespace Araneum\Base\Ali\DatatableBundle\Util\Factory\Query;

use Ali\DatatableBundle\Util\Factory\Query\DoctrineBuilder;
use Araneum\Base\Ali\Helper\TimeCounterHelper;

class AraneumDoctrineBuilder extends DoctrineBuilder
{
    protected $searchQuery;
    protected $isEmptyQuery = false;

    /**
     * Set search query
     *
     * @param $searchQuery
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;
    }

    /**
     * Get search query
     *
     * @return mixed
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    /**
     * Search case insensitive like
     *
     * @param $search_field
     * @param $value
     * @return string
     */
    public function searchLike($search_field, $value)
    {
        return " UPPER($search_field) LIKE UPPER('%{$value}%') ";
    }

    /**
     * Search equals
     *
     * @param $search_field
     * @param $value
     * @return string
     */
    public function searchEquals($search_field, $value)
    {
        return " $search_field = '$value' ";
    }

    /**
     * Search by date interval changed with the date format
     *
     * @param string $search_field
     * @param string $date
     * @return string
     */
    public function searchDateIntervalDay($search_field, $date)
    {
        try {
            if (!preg_match('/^\d{4}/', $date)) {
                return false;
            }

            $dateObject = new \DateTime($date);
            $dateObject->setTime(0, 0, 0);
        } catch (\Exception $e) {
            return false;
        }

        if (preg_match('/^\d{4}$/', $date)) {
            $dateObject->setDate($date, 1, 1);
            $dtFrom = $dateObject->modify('first day of this year')->format('Y-m-d H:i:s.u');
            $dtTo = $dateObject->modify('first day of next year')->format('Y-m-d H:i:s.u');
        } elseif (preg_match('/^\d{4}-\d{2}$/', $date)) {
            $dtFrom = $dateObject->modify('first day of this month')->format('Y-m-d H:i:s.u');
            $dtTo = $dateObject->modify('first day of next month')->format('Y-m-d H:i:s.u');
        } else {
            $dtFrom = $dateObject->format('Y-m-d H:i:s.u');
            $dtTo = $dateObject->modify('+1 day')->format('Y-m-d H:i:s.u');
        }

        return " ( $search_field > '$dtFrom' AND $search_field < '$dtTo' )  ";
    }

    /**
     * Search in array
     *
     * @param string $search_field
     * @param string $value
     * @return string
     */
    public function searchIn($search_field, $value)
    {
        return " $search_field IN (" . implode(',', $value) . ') ';
    }

    /**
     * Search time
     *
     * @param $search_field
     * @param $value
     * @return string
     */
    public function searchTime($search_field, $value)
    {
        if (is_array($value)) {
            $timeInHour = TimeCounterHelper::TIME_IN_HOUR;
            $timeInMinute = TimeCounterHelper::TIME_IN_MINUTE;

            $timeArray = $value;
            $hours = (int)$timeArray[0];
            $seconds = (int)$timeArray[1];
            $minusesWithHour = (int)$timeArray[1];
            $minusesWithSecond = (int)$timeArray[0];

            $searchByHourAndMinutes = "($search_field / $timeInHour = $hours
            AND MOD($search_field , $timeInHour) / $timeInMinute = $minusesWithHour
            )";
            $searchByMinutesAndSeconds = "(
                MOD($search_field , $timeInHour) / $timeInMinute = $minusesWithSecond
                AND MOD(MOD($search_field , $timeInHour) , $timeInMinute) = $seconds
            )";

            return $searchByHourAndMinutes . " OR " . $searchByMinutesAndSeconds;
        }

        return false;
    }

    /**
     * Get the search dql
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return string|void
     */
    protected function _addSearch(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        if ($this->search == true) {

            $request = $this->request;
            $search_fields = array_keys($this->getSearchQuery());

            $search_param = trim($request->get("sSearch"));
            if (empty($search_param)) {
                return;
            }

            $orQueries = [];
            $searchQueryArray = $this->getSearchQuery();
            foreach ($search_fields as $i => $search_field) {
                if (!isset($searchQueryArray[$search_field])) {
                    continue;
                }

                $field = explode(' ', trim($search_field));
                $search_field = $field[0];
                if (($oneQuery = $searchQueryArray[$search_field]($this, $search_field, $search_param)) !== false &&
                    !empty($oneQuery)
                ) {
                    $orQueries[] = $oneQuery;
                }
            }

            if (empty($orQueries)) {
               $this->setIsEmptyQuery(true);
            }

            if (!empty($orQueries)) {
                $orQuery = '(' . implode(' OR ', $orQueries) . ' ) ';
                $queryBuilder->andWhere($orQuery);
            }
        }
    }

    /**
     * Get QueryBuilder with search query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getResultQueryBuilder()
    {
        $qb = clone $this->queryBuilder;
        $this->_addSearch($qb);

        return $qb;
    }

    /**
     * Adds an item that is to be returned in the query result.
     *
     * @param null $select
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addSelect($select = null)
    {
        return $this->queryBuilder->addSelect($select);
    }

    /**
     * Is empty query
     *
     * @return boolean
     */
    public function isEmptyQuery()
    {
        return $this->isEmptyQuery;
    }

    /**
     * Set is empty query
     *
     * @param boolean $isEmptyQuery
     */
    public function setIsEmptyQuery($isEmptyQuery)
    {
        $this->isEmptyQuery = $isEmptyQuery;
    }

    /**
     * Get data
     *
     * @param int $hydration_mode
     * @return array
     */
    public function getData($hydration_mode)
    {
        if ($this->isEmptyQuery()) {
            return [
                [],
                []
            ];
        }

        return parent::getData($hydration_mode);
    }

    /**
     * Get total count
     *
     * @return int|mixed
     */
    public function getTotalRecords()
    {
        $qb = clone $this->queryBuilder;
        $this->_addSearch($qb);

        if ($this->isEmptyQuery()) {
            return 0;
        }

        $qb->resetDQLPart('orderBy');

        $gb = $qb->getDQLPart('groupBy');
        if (empty($gb) || !in_array($this->fields['_identifier_'], $gb)) {
            $qb->select(" count({$this->fields['_identifier_']}) ");

            return $qb->getQuery()->getSingleScalarResult();
        } else {
            $qb->resetDQLPart('groupBy');
            $qb->select(" count(distinct {$this->fields['_identifier_']}) ");

            return $qb->getQuery()->getSingleScalarResult();
        }
    }
}
