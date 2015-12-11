<?php

namespace Araneum\Base\Ali\DatatableBundle\Util;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ali\DatatableBundle\Util\Datatable;

/**
 * Class AraneumDatatable
 *
 * @package Araneum\Base\Ali\DatatableBundle\Util
 */
class AraneumDatatable extends Datatable
{
    /**
     * AraneumDatatable constructor.
     *
     * @param ContainerInterface $container
     * @param QueryBuilder       $queryBuilder
     */
    public function __construct(ContainerInterface $container, QueryBuilder $queryBuilder)
    {
        parent::__construct($container);
        $this->_queryBuilder = $queryBuilder;
    }

    /**
     * Get Datatable Columns
     *
     * @return array
     */
    public function getFieldLabels()
    {
        $fields = $this->getFields();

        if (array_key_exists('_identifier_', $fields)) {
            unset($fields['_identifier_']);
        }

        return array_keys($fields);
    }

    /**
     * Get data without page limits
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->_queryBuilder->getResultQueryBuilder()->getQuery()->getResult();
    }

    /**
     * Get total records
     *
     * @return int
     */
    public function getTotalRecords()
    {
        return $this->_queryBuilder->getTotalRecords();
    }

    /**
     * Get search query
     *
     * @param string $searchQuery
     * @return $this
     */
    public function setSearchQuery($searchQuery)
    {
        $this->_queryBuilder->setSearchQuery($searchQuery);

        return $this;
    }
}
