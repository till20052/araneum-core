<?php

namespace Araneum\Base\Service;


class AdminInitializerService
{
    private $datatableFactory;

    private $formExporter;

    private $filter;

    private $action;

    private $grid;

    private $error = [];

    /**
     * AdminInitializerService constructor.
     *
     * @param $formExporter
     * @param $datatableFactory
     */
    public function __construct($formExporter, $datatableFactory)
    {
        $this->formExporter = $formExporter;
        $this->datatableFactory = $datatableFactory;
    }

    /**
     * Return initial array
     *
     * @return mixed
     */
    public function get()
    {
        $result = [
            'filter' => $this->filter,
            'action' => $this->action,
            'grid' => $this->grid
        ];

        if(count($this->error)) {
            $result['errors'] = $this->error;
        }

        return $result;
    }

    /**
     * Set filters
     *
     * @param $filter
     * @return mixed
     */
    public function setFilters($filter)
    {
        $this->filter = $this->formExporter->get($filter);

        return $this;
    }

    /**
     * Set actions
     *
     * @param $action
     * @return mixed
     */
    public function setActions($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set grid
     *
     * @param $gridType
     * @param $source
     * @return $this
     */
    public function setGrid($gridType, $source)
    {
        $this->grid = [
            'columns' => $this->datatableFactory->create($gridType)->getFieldLabels(),
            'source' => $source,
        ];

        return $this;
    }

    /**
     * Set error
     *
     * @param \Exception $error
     */
    public function setError(\Exception $error)
    {
        $this->error[] = $error->getMessage();
    }
}