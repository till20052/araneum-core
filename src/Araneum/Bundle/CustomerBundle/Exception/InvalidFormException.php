<?php

namespace Araneum\Bundle\CustomerBundle\Exception;

use Symfony\Component\Form\Form;

class InvalidFormException extends \Exception implements InvalidFormExceptionInterface
{

    private $statusCode;

    /**
     * Constructor
     *
     * @param string $message
     * @param Form   $form
     */
    public function __construct($message, Form $form)
    {
        $this->statusCode = 500;
        $message = $message . ' ' . $this->getFormErrorMessage($form);
        parent::__construct($message, $this->statusCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormErrorMessage(Form $form)
    {
        $msg = (string)$form->getErrors(true, false);

        return $msg;
    }
}