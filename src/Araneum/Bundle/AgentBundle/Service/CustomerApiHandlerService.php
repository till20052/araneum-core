<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\CustomersLog;
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
    protected $spotOption;
    protected $formFactory;

    /**
     * Class construct
     *
     * @param EntityManager      $entityManager
     * @param ApplicationManagerService $appManager
     * @param FormFactory               $formFactory
     * @param SpotOptionService         $spotOption
     */
    public function __construct(
        EntityManager $entityManager,
        ApplicationManagerService $appManager,
        FormFactory $formFactory,
        SpotOptionService $spotOption
    )
    {
        $this->entityManager = $entityManager;
        $this->appManager = $appManager;
        $this->formFactory = $formFactory;
        $this->spotOption = $spotOption;
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
        $form = $this->formFactory->create(new CustomerType(), $customer);

        $form->submit($parameters);

        if ($form->isValid()) {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return $customer;
        } else {
            throw new InvalidFormException($form, 'Invalid submitted data');
        }
    }

    public function login($email, $password, $appKey)
    {
        $application = $this->appManager
            ->findOneOr404(['appKey' => $appKey]);

        $spotResponse = $this->spotOption->login($email, $password);

        $customer = $this->entityManager
            ->getRepository('AraneumAgentBundle:Customer')
            ->findOneBy(['email' => $email]);
        $log = new CustomersLog();
        $log->setApplication($application);
        $log->setAction('Login');
        $log->setCustomer($customer);
        $log->setSpotResponse($spotResponse);

        //TODO respnonse spotoption description
        if ($spotResponse) {
            $log->setStatus(CustomersLog::STATUS_SUCCESS);
        } else {
            $log->setStatus(CustomersLog::STATUS_ERROR);
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log->getStatus();
    }
}