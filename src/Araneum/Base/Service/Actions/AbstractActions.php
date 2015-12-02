<?php

namespace Araneum\Base\Service\Actions;


abstract class AbstractActions
{
    /**
     * Build the list
     *
     * @param  $builder
     * @return null
     */
    abstract public function buildActions(ActionBuilderInterface $builder);
}
