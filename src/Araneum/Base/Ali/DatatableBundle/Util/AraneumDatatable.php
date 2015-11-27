<?php

namespace Araneum\Base\Ali\DatatableBundle\Util;

use Doctrine\ORM\Query;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ali\DatatableBundle\Util\Datatable;
use Symfony\Component\HttpFoundation\JsonResponse;

class AraneumDatatable extends Datatable
{
    /**
     * Class constructor
     *
     * @param ContainerInterface $container
     * @param                    $queryBuilder
     */
    public function __construct(ContainerInterface $container, $queryBuilder)
    {
        parent::__construct($container);
        $this->_queryBuilder = $queryBuilder;
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
     * @param $searchQuery
     * @return $this
     */
    public function setSearchQuery($searchQuery)
    {
        $this->_queryBuilder->setSearchQuery($searchQuery);

        return $this;
    }

    /**
     * @param int $hydration_mode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function execute($hydration_mode = Query::HYDRATE_ARRAY)
    {
        /** @var JsonResponse $jsonResponse */
        $jsonResponse = parent::execute($hydration_mode);

        $jsonResponse->setData(
			json_decode($jsonResponse->getContent(), true) + ['headers' => $this->getFieldsLabels($this->getFields())]
        );

        return $jsonResponse;
    }

	/**
	 * Get from fields definition labels without _identifier_
	 *
	 * @param $fields
	 * @return array
	 */
	private function getFieldsLabels($fields)
	{
		if (array_key_exists('_identifier_', $fields)) {
			unset($fields['_identifier_']);
		}

		return array_keys($fields);
	}
}
