<?php

namespace Araneum\Base\Service;


class AdminInitializerService
{
    private $formExporter;

    private $filter;

    private $action;

    private $grid;

    private $error = [];

    /**
     * AdminInitializerService constructor.
     *
     * @param $formExporter
     */
    public function __construct($formExporter)
    {
        $this->formExporter = $formExporter;
    }

    /**
     * Return initial array
     *
     * @return mixed
     */
    public function get()
    {
        return [
            'filter' => $this->filter,
            'action' => $this->action,
            'grid' => $this->grid
        ];
    }

    /**
     * Set filters
     *
     * @param $filter
     * @return mixed
     */
    public function setFilters($filter)
    {
        return $this->filter = $this->formExporter->get($filter);
    }

    /**
     * Set actions
     *
     * @param $action
     * @return mixed
     */
    public function setActions($action)
    {
        return $this->action = $action;
    }

    /**
     * Set grid
     *
     * @param $grid
     * @return mixed
     */
    public function setGrid($grid)
    {
        return $this->grid = $grid;
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