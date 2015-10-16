<?php

namespace Araneum\Bundle\CustomerBundle\Exception;

use Symfony\Component\Form\Form;

/**
 * Interface InvalidFormExceptionInterface
 *
 * @package Araneum\Bundle\CustomerBundle\Exception
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