<?php

namespace Araneum\Bundle\AgentBundle\Exception;

use Symfony\Component\Form\Form;

/**
 * Interface InvalidFormExceptionInterface
 *
 * @package Araneum\Bundle\AgentBundle\Exception
 */
interface InvalidFormExceptionInterface
{
    /**
     * Return message form
     *
     * @param Form $form
     * @return mixed
     */
    public function getFormErrorMessage(Form $form);
}