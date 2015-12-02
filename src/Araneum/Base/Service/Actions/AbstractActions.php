<?php

namespace Araneum\Base\Service\Actions;


abstract class AbstractActions
{
    /**
     * Build the list
     *
     * @param  $builder
     */
    abstract public function buildActions(ActionBuilderInterface $builder);
}
