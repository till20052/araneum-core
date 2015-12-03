<?php

namespace Araneum\Base\Service;

use Araneum\Base\Ali\DatatableBundle\Util\Factory\DatatableFactory;
use Araneum\Base\Service\Actions\AbstractActions;
use Araneum\Base\Service\Actions\ActionFactory;

class AdminInitializerService
{
    private $datatableFactory;

    private $formExporter;

    private $actionFactory;

    private $filter;

    private $action;

    private $grid;

    private $error = [];

    /**
     * AdminInitializerService constructor.
     *
     * @param FromExporterService $formExporter
     * @param DatatableFactory $datatableFactory
     * @param ActionFactory $actionFactory
     */
    public function __construct($formExporter, $datatableFactory, $actionFactory)
    {
        $this->formExporter = $formExporter;
        $this->datatableFactory = $datatableFactory;
        $this->actionFactory = $actionFactory;
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
     * @param AbstractActions $action
     * @return mixed
     */
    public function setActions(AbstractActions $action)
    {
        $this->action = $this->actionFactory->create($action);

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