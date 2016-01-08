<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\Error;
use Araneum\Bundle\AgentBundle\Form\Type\ErrorType;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Araneum\Base\Exception\InvalidFormException;
use Symfony\Component\Form\FormFactory;

/**
 * Class ErrorApiHandlerService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class ErrorApiHandlerService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ApplicationManagerService
     */
    protected $appManager;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * ErrorApiHandlerService constructor.
     *
     * @param \Doctrine\ORM\EntityManager                                  $em
     * @param \Symfony\Component\Form\FormFactory                          $formFactory
     * @param \Araneum\Bundle\MainBundle\Service\ApplicationManagerService $applicationManager
     */
    public function __construct(
        EntityManager $em,
        FormFactory $formFactory,
        ApplicationManagerService $applicationManager
    ) {
        $this->entityManager = $em;
        $this->formFactory = $formFactory;
        $this->appManager = $applicationManager;
    }

    /**
     * Post error data
     *
     * @param string $appKey
     * @param array  $parameters
     * @return array
     */
    public function post($appKey, array $parameters)
    {
        $application = $this->appManager->findOneOr404(['appKey' => $appKey]);

        $error = new Error();
        $error->setApplication($application);

        return $this->processForm($parameters, $error);
    }

    /**
     * Process Form
     *
     * @param array $parameters
     * @param Error $error
     * @return array $error
     * @throws InvalidFormException
     */
    public function processForm(array $parameters, $error)
    {
        $form = $this->formFactory->create(new ErrorType(), $error);
        $form->submit($parameters);

        if ($form->isValid()) {
            $this->entityManager->persist($error);
            $this->entityManager->flush();

            return [
                'id' => $error->getId(),
                'message' => 'Error has been inserted',
            ];
        } else {
            throw new InvalidFormException($form, 'Invalid submitted data');
        }
    }
}
