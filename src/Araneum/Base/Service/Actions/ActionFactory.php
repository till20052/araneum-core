<?php

namespace Araneum\Base\Service\Actions;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ActionFactory
 *
 * @package Araneum\Base\Service\Actions
 */
class ActionFactory
{
    private $builder;

    /**
     * ActionFactory constructor.
     *
     * @param ActionBuilder $builder
     */
    public function __construct(ActionBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Create action list
     *
     * @param AbstractActions $actions
     * @return array
     */
    public function create(AbstractActions $actions)
    {
        $actions->buildActions($this->builder);

        return $this->builder->getActions();
    }

    /**
     * Return JsonResponse encoded action list
     *
     * @param AbstractActions $actions
     * @return string
     */
    public function createJsonResponse(AbstractActions $actions)
    {
        return new JsonResponse($this->create($actions));
    }
}
