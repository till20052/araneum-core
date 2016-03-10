<?php

namespace Araneum\Base\Service\Actions;

/**
 * Class AbstractActions
 *
 * @package Araneum\Base\Service\Actions
 */
abstract class AbstractActions
{
    /**
     * Build the list
     *
     * @param ActionBuilderInterface $builder
     */
    abstract public function buildActions(ActionBuilderInterface $builder);
}
