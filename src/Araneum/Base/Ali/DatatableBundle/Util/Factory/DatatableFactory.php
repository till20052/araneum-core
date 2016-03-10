<?php

namespace Araneum\Base\Ali\DatatableBundle\Util\Factory;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\AnnotationReader;
use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilder;
use Araneum\Base\Ali\DatatableBundle\Util\AraneumDatatable;
use Araneum\Base\Ali\Helper\ArrayHelper;

/**
 * Class DatatableFactory
 *
 * @package Araneum\Base\Ali\DatatableBundle\Util\Factory
 */
class DatatableFactory
{
    /**
     * @var $datatable AraneumDatatable
     */
    private $datatable;

    /**
     * @var  Registry
     */
    private $doctrine;

    /**
     * @var
     */
    private $templating;

    /**
     * @var  AnnotationReader
     */
    private $annotationReader;

    /**
     * @var
     */
    private $user;

    /**
     * @var string
     */
    private $entityAlias = 'x';

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var array
     */
    private $joinColumn = [];

    /**
     * @var array
     */
    private $searchQuery = [];

    /**
     * @var array
     */
    private static $postgresNumericTypesRange = [
        'smallint' => [
            'min' => -32768,
            'max' => 32768,
        ],
        'integer' => [
            'min' => -2147483648,
            'max' => 2147483648,
        ],
        'bigint' => [
            'min' => -9223372036854775808,
            'max' => 9223372036854775808,
        ],
        'decimal' => [
            'min' => INF * -1,
            'max' => INF,
        ],
        'float' => [
            'min' => INF * -1,
            'max' => INF,
        ],
    ];

    const DEFAULT_DATE_FORMAT = 'Y-m-d, H:i:s';

    /**
     * DatatableFactory constructor.
     *
     * @param object $datatable
     * @param object $doctrine
     * @param object $templating
     * @param object $annotationReader
     * @param object $securityContext
     */
    public function __construct($datatable, $doctrine, $templating, $annotationReader, $securityContext)
    {
        $this->datatable = $datatable;
        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->annotationReader = $annotationReader;
        $this->user = $securityContext->getToken()->getUser();
    }

    /**
     * Create configured datatable
     *
     * @param  AbstractList $list
     * @return AraneumDatatable|object
     */
    public function create(AbstractList $list)
    {
        $builderList = new ListBuilder();

        $list->buildList($builderList);
        $entity = $this->getEntityClassNameByAlias($list->getEntityClass());
        $listField = $builderList->getList();

        $queryBuilder = $list->createQueryBuilder($this->doctrine);
        if ($queryBuilder) {
            $this->entityAlias = $queryBuilder->getRootAlias();
        }

        array_walk(
            $listField,
            function (&$description, $name) use ($entity) {
                $this->prepareField($entity, $name, $description);
                $this->prepareSearch($name, $description);
            }
        );

        if (!is_null($builderList->getWidgetData())) {
            $listField = $this->prepareWidget($builderList, $listField);
        }

        $this->fields['_identifier_'] = $this->getQueryFieldName('id');

        $this->datatable->setFields($this->fields);
        $this->datatable->setSearchQuery($this->searchQuery);
        $this->datatable->setRenderer(
            function (&$data) use ($listField) {
                $this->setRenderFields($data, $listField, $this);
            }
        );

        if ($queryBuilder) {
            if (!empty($this->joinColumn)) {
                foreach ($this->joinColumn as $column) {
                    $field = $column['field'];
                    $queryBuilder->addSelect($field);
                    if ($this->isJoinExist($queryBuilder, $field) === false) {
                        if (array_key_exists('relation', $column)) {
                            $queryBuilder->innerJoin($this->getQueryFieldName($field, $column['relation']), $field);
                        } else {
                            $queryBuilder->innerJoin($this->getQueryFieldName($field), $field);
                        }
                    }
                }
            }

            $this->datatable->getQueryBuilder()->setDoctrineQueryBuilder($queryBuilder);
        } else {
            $this->datatable->setEntity($list->getEntityClass(), $this->entityAlias);
        }

        if ($orderBy = $builderList->getOrderBy()) {
            $order = strpos($orderBy['field'], '.') !== false
                ? $orderBy['field']
                : $this->getQueryFieldName(
                    $orderBy['field']
                );
            $this->datatable->setOrder(
                $order,
                $orderBy['sort']
            );
        }

        $this->datatable
            ->setSearch($builderList->isSearchEnabled())
            ->setHasAction(false);

        return $this->datatable;
    }

    /**
     * Set render fields
     *
     * @param  array  $data
     * @param  array  $listField
     * @param  object $this1
     * @return mixed
     */
    public function setRenderFields(&$data, $listField, $this1)
    {
        foreach ($data as $key => $value) {
            foreach ($listField as $fieldDescription) {
                if (array_key_exists('column', $fieldDescription) && $fieldDescription['column'] === $key
                    && array_key_exists('render', $fieldDescription) && !empty($fieldDescription['render'])
                ) {
                    $data[$key] = $fieldDescription['render'](
                        $value,
                        $data,
                        $this1->doctrine,
                        $this1->templating,
                        $this1->user
                    );
                    break;
                }
            }
        }
    }

    /**
     * Get filed with alias dot separated
     *
     * @param  string $name
     * @param  null   $alias
     * @return string
     */
    public function getQueryFieldName($name, $alias = null)
    {
        $alias = $alias ?: $this->entityAlias;

        return $alias.'.'.$name;
    }

    /**
     * Add Search condition to QueryBuilder
     *
     * @param  string $name
     * @param  string $description
     * @throws \Exception
     */
    public function prepareSearch($name, &$description)
    {
        if (array_key_exists('search_type', $description) && !empty($description['search_type'])
            && !array_key_exists('custom_search', $description) && empty($description['custom_search'])
        ) {
            switch ($description['search_type']) {
                case 'datetime':
                    $this->searchQuery[$this->getSearchField($name, $description)] =
                        function ($thisBuilder, $searchField, $value) {

                            return $thisBuilder->searchDateIntervalDay($searchField, $value);
                        };
                    break;

                case 'like':
                    if ($this->isFieldNumericType($description) === true) {
                        throw new \Exception('Not supported field for like search field'.$name);
                    }
                    $this->searchQuery[$this->getSearchField($name, $description)] =
                        function ($thisBuilder, $searchField, $value) {

                            return $thisBuilder->searchLike($searchField, $value);
                        };
                    break;

                case 'like_array':
                    if (!array_key_exists('search_array', $description) || empty($description['search_array'])) {
                        throw new \Exception('Specify search_array field for column'.$name);
                    }

                    $this->searchQuery[$this->getSearchField($name, $description)] =
                        function ($thisBuilder, $searchField, $value) use ($description) {
                            $arrayKeys = ArrayHelper::searchLike($value, $description['search_array']);

                            if (empty($arrayKeys)) {
                                return false;
                            }

                            return $thisBuilder->searchIn($searchField, array_keys($arrayKeys));
                        };
                    break;

                case 'equals':
                    if ($this->isFieldNumericType($description) === true) {
                        $maxNumber = self::$postgresNumericTypesRange[$description['columnType']]['max'];
                        $minNumber = self::$postgresNumericTypesRange[$description['columnType']]['min'];
                        $this->searchQuery[$this->getSearchField($name, $description)] =
                            function ($thisBuilder, $searchField, $value) use ($maxNumber, $minNumber) {
                                if (!is_numeric($value) || $value >= $maxNumber || $value <= $minNumber) {
                                    return false;
                                }

                                return $thisBuilder->searchEquals($searchField, $value);
                            };
                    } else {

                        $this->searchQuery[$this->getSearchField($name, $description)] =
                        function ($thisBuilder, $searchField, $value) {
                            return $thisBuilder->searchEquals($searchField, $value);
                        };
                    }

                    break;
                case 'callback':
                    if (!array_key_exists('callback_function', $description)
                        || empty($description['callback_function'])
                    ) {
                        throw new \Exception('Specify callback_function field for column'.$name);
                    }

                    $doctrine = $this->doctrine;
                    $this->searchQuery[$this->getSearchField($name, $description)] =
                        function ($thisBuilder, $searchField, $value) use ($description, $doctrine) {
                            return $description['callback_function']($thisBuilder, $searchField, $value, $doctrine);
                        };

                    break;
                default:
                    throw new \Exception('Unsupported search type');
            }
        }

        if (array_key_exists('custom_search', $description) && !empty($description['custom_search'])) {
            $this->searchQuery[$this->getQueryFieldName($name)] = $description['custom_search'];
        }
    }

    /**
     * Check for join exists
     *
     * @param  $queryBuilder
     * @param  $alias
     * @return bool
     */
    private function isJoinExist($queryBuilder, $alias)
    {
        $joinDqlParts = $queryBuilder->getDQLParts()['join'];
        foreach ($joinDqlParts as $joins) {
            foreach ($joins as $join) {
                if ($join->getAlias() === $alias) {

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get fully qualified class name
     *
     * @param  $alias
     * @return string
     */
    private function getEntityClassNameByAlias($alias)
    {
        preg_match_all('/[A-Z]/', $alias, $matches, PREG_OFFSET_CAPTURE);
        $bundlePosition = $matches[0][1][1];
        $vendor = substr($alias, 0, $bundlePosition);
        $bundle = substr($alias, $bundlePosition, strpos($alias, ':') - $bundlePosition);
        $entity = substr($alias, strpos($alias, ':') + 1);

        return $vendor.'\\Bundle\\'.$bundle.'\\Entity\\'.$entity;
    }

    /**
     * Prepare single field
     *
     * @param $entity
     * @param $name
     * @param $description
     */
    private function prepareField($entity, $name, &$description)
    {
        $foreignTableColumn = null;
        if (strpos($name, '.') !== false) {
            $foreignTableColumn = substr($name, strpos($name, '.') + 1);
            $reference = substr($name, 0, strpos($name, '.'));
            if (strpos($foreignTableColumn, '.') !== false) {
                $secondReference = substr($foreignTableColumn, 0, strpos($foreignTableColumn, '.'));
                $foreignTableColumn = substr($foreignTableColumn, strpos($foreignTableColumn, '.') + 1);

                $joinField = ['field' => $reference];
                if (!in_array($joinField, $this->joinColumn)) {
                    $this->joinColumn[] = $joinField;
                }

                $secondJoinField = [
                    'field' => $secondReference,
                    'relation' => $reference,
                ];
                if (!in_array($secondJoinField, $this->joinColumn)) {
                    $this->joinColumn[] = $secondJoinField;
                }

                $referenceEntity = $this->getMappingClass($entity, $reference);
                $secondReferenceEntity = $this->getMappingClass($referenceEntity, $secondReference);
                $this->setDescriptionByType($secondReferenceEntity, $foreignTableColumn, $description);

                $description['searchField'] = $this->getQueryFieldName(
                    $foreignTableColumn,
                    $secondReference
                );
                $fieldName = empty($description['label']) ? ucfirst($secondReference) : $description['label'];
                $this->fields[$fieldName] = $this->getQueryFieldName($foreignTableColumn, $secondReference);
            } else {
                $entity = $this->getMappingClass($entity, $reference);
                $this->setDescriptionByType($entity, $foreignTableColumn, $description);

                $joinField = ['field' => $reference];
                if (!in_array($joinField, $this->joinColumn)) {
                    $this->joinColumn[] = $joinField;
                }

                $description['searchField'] = $this->getQueryFieldName($foreignTableColumn, $reference);
                $fieldName = empty($description['label']) ? ucfirst($reference) : $description['label'];
                $this->fields[$fieldName] = $this->getQueryFieldName($foreignTableColumn, $reference);
            }
        } else {
            $this->setDescriptionByType($entity, $name, $description);
            $fieldName = empty($description['label']) ? ucfirst($name) : $description['label'];
            $this->fields[$fieldName] = $this->getQueryFieldName($name);
        }
    }

    /**
     * Guest from annotation field type(only for datatime, date)
     *
     * @param object $entity
     * @param string $name
     * @param string $description
     */
    private function setDescriptionByType($entity, $name, &$description)
    {
        $trimName = trim($name);
        if (empty($trimName)) {
            return;
        }

        $column = $this->annotationReader->getPropertyAnnotation(
            new \ReflectionProperty($entity, $name),
            'Doctrine\\ORM\\Mapping\\Column'
        );

        if ($column && !empty($column->type)) {
            $description['columnType'] = $column->type;
            if (strtolower($column->type) == 'datetime') {
                if (!array_key_exists('date_format', $description)) {
                    $description['date_format'] = self::DEFAULT_DATE_FORMAT;
                }

                if (!array_key_exists('render', $description)) {
                    $description['render'] = function ($value) use (
                        $description
                    ) {
                        return $value instanceof \DateTime ? $value->format($description['date_format']) : '';
                    };
                }
            } elseif (strtolower($column->type) == 'date') {
                if (!array_key_exists('date_format', $description)) {
                    $description['date_format'] = 'Y-m-d';
                }

                if (!array_key_exists('render', $description)) {
                    $description['render'] = function ($value) use (
                        $description
                    ) {
                        return $value instanceof \DateTime ? $value->format($description['date_format']) : '';
                    };
                }
            }
        }
    }

    /**
     * Get mapped class
     *
     * @param  object $entity
     * @param  string $reference
     * @return bool
     * @throws \Exception
     */
    private function getMappingClass($entity, $reference)
    {
        $relationAnnotations = [
            'Doctrine\\ORM\\Mapping\\ManyToOne',
            'Doctrine\\ORM\\Mapping\\OneToOne',
            'Doctrine\\ORM\\Mapping\\ManyToMany',
            'Doctrine\\ORM\\Mapping\\OneToMany',
        ];

        foreach ($relationAnnotations as $relationAnnotation) {
            if ($this->getTargetEntity($entity, $reference, $relationAnnotation) !== false) {
                return $this->getTargetEntity($entity, $reference, $relationAnnotation);
            }
        }

        throw new \Exception('Not found mapping information for column '.$reference.' in class '.$entity);
    }

    /**
     * Get target entity from mapping annotation
     *
     * @param  object $entity
     * @param  string $reference
     * @param  string $annotation
     * @return bool
     */
    private function getTargetEntity($entity, $reference, $annotation)
    {
        $relation = $this->annotationReader->getPropertyAnnotation(
            new \ReflectionProperty($entity, $reference),
            $annotation
        );
        if (!empty($relation) && !empty($relation->targetEntity)) {
            return $relation->targetEntity;
        }

        return false;
    }

    /**
     * Return field for search
     *
     * @param  string $name
     * @param  string $description
     * @return string
     */
    private function getSearchField($name, $description)
    {
        return array_key_exists('searchField', $description)
            ? $description['searchField']
            : $this->getQueryFieldName($name);
    }

    /**
     * Check is field numeric
     *
     * @param  string $description
     * @return bool
     */
    private function isFieldNumericType(&$description)
    {
        if (!array_key_exists('columnType', $description)) {
            return false;
        }

        return
            $description['columnType'] == 'smallint' ||
            $description['columnType'] == 'integer' ||
            $description['columnType'] == 'bigint' ||
            $description['columnType'] == 'decimal' ||
            $description['columnType'] == 'float';
    }

    /**
     * Prepare widget
     *
     * @param  string $builderList
     * @param  string $listField
     * @return mixed
     */
    private function prepareWidget($builderList, $listField)
    {
        $widgetData = $builderList->getWidgetData();
        $this->fields['_hide_'] = $this->getQueryFieldName('id');
        $listField['_hide_'] = [
            'column' => count($listField),
            'render' =>
                function ($value, $data, $doctrine, $templating, $user) use ($widgetData) {
                    $widgetArray = $widgetData($value, $data, $doctrine, $templating, $user);
                    if (!is_array($widgetArray)) {
                        throw new \Exception('Widget closure must return array');
                    }

                    $html[] = '_hide_';
                    foreach ($widgetArray as $name => $field) {
                        $html[$name] = $field;
                    }

                    return json_encode($html);
                },
        ];

        return $listField;
    }
}
