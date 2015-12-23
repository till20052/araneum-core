<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\Error;
use Araneum\Bundle\AgentBundle\Form\Type\ErrorType;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Base\Exception\InvalidFormException;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;

/**
 * Class ErrorApiHandlerService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class ErrorApiHandlerService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

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
     * Service Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get Customer
     *
     * @param string $appKey the application appKey
     * @param array  $parameters
     * @return array
     */
    public function post($appKey, array $parameters)
    {
        $application = $this->getAppManager()->findOneOr404(['appKey' => $appKey]);

        $customer = new Error();
        $customer->setApplication($application);

        return $this->processForm($parameters, $customer);
    }

    /**
     * Process Form
     *
     * @param array $parameters
     * @param Error $error
     * @return Customer $customer
     * @throws InvalidFormException
     */
    public function processForm(array $parameters, $error)
    {
        $em = $this->getEntityManager();
        $form = $this->container->get('form.factory')->create(new ErrorType(), $error);
        $form->submit($parameters);

        if ($form->isValid()) {
            $em->persist($error);
            $em->flush();

            return $error;
        } else {
            throw new InvalidFormException($form, 'Invalid submitted data');
        }
    }

    /**
     * Get entity Manager
     *
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if (is_null($this->entityManager)) {
            $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        }

        return $this->entityManager;
    }

    /**
     * Get application Manager
     *
     * @return ApplicationManagerService
     */
    private function getAppManager()
    {
        if (is_null($this->appManager)) {
            $this->appManager = $this->container->get('araneum.main.application.manager');
        }

        return $this->appManager;
    }
}
