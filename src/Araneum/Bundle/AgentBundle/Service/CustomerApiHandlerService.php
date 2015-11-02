<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Form\CustomerType;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Base\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;

class CustomerApiHandlerService
{
    protected $entityManager;
    protected $appManager;

    protected $form;

    /**
     * Class construct
     *
     * @param EntityManager      $entityManager
     * @param ApplicationManagerService $appManager
     * @param FormFactory               $formFactory
     */
    public function __construct(
        EntityManager $entityManager,
        ApplicationManagerService $appManager,
        FormFactory $formFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->appManager = $appManager;
        $this->form = $formFactory;
    }

    /**
     * Get Customer
     *
     * @param string   $appKey the application appKey
     * @param array    $parameters
     * @return array
     */
    public function post($appKey, array $parameters)
    {
        $appManager = $this->appManager;
        $application = $appManager->findOneOr404(['appKey' => $appKey]);

        $customer = new Customer();
        $customer->setApplication($application);

        return $this->processForm($parameters, $customer);
    }

    /**
     * Process Form
     *
     * @param Array    $parameters
     * @param Customer $customer
     * @return Customer $customer
     * @throws InvalidFormException
     */
    public function processForm(array $parameters, $customer)
    {
        $form = $this->form->create(new CustomerType(), $customer);

        $form->submit($parameters);

        if ($form->isValid()) {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return $customer;
        } else {
            throw new InvalidFormException($form, 'Invalid submitted data');
        }
    }
}