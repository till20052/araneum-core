<?php

namespace Araneum\Bundle\CustomerBundle\Service;

use Araneum\Bundle\CustomerBundle\Form\CustomerType;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Araneum\Bundle\CustomerBundle\Entity\Customer;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\HttpFoundation\Request;
use Araneum\Bundle\CustomerBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

class CustomerApiHandlerService
{
    protected $entityManager;
    protected $appManager;

    /**
     * Class construct
     *
     * @param EntityManager      $entityManager
     * @param ApplicationManagerService $appManager
     */
    public function __construct(EntityManager $entityManager, ApplicationManagerService $appManager)
    {
        $this->entityManager = $entityManager;
        $this->appManager = $appManager;
    }

    /**
     * Get Customer
     *
     * @param string   $appKey the application appKey
     * @param array    $parameters
     * @param Form     $form
     * @param Customer $customer
     * @return array
     */
    public function getCustomer($appKey, array $parameters, $form, $customer)
    {
        $appManager = $this->appManager;
        $application = $appManager->findOneOr404(['appKey' => $appKey]);

        $customer->setApplication($application);

        return $this->processForm($parameters, $customer, $form);
    }

    /**
     * Process Form
     *
     * @param Array    $parameters
     * @param Customer $customer
     * @param Form     $form
     * @return Customer $customer
     * @throws InvalidFormException
     */
    public function processForm(array $parameters, Customer $customer, Form $form)
    {
        $form->submit($parameters);

        if ($form->isValid()) {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return $customer;
        } else {
            throw new InvalidFormException('Invalid submitted data', $form);
        }
    }
}