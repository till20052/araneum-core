<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\CustomersLog;
use Araneum\Bundle\AgentBundle\Form\CustomerType;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Base\Exception\InvalidFormException;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
        $application = $this->getAppManager()->findOneOr404(['appKey' => $appKey]);

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
        $form = $this->container->get('form.factory')->create(new CustomerType(), $customer);
        $form->submit($parameters);

        if ($form->isValid()) {
            $this->getEntityManager()->persist($customer);
            $this->getEntityManager()->flush();

            return $customer;
        } else {
            throw new InvalidFormException($form, 'Invalid submitted data');
        }
    }

    public function login($email, $password, $appKey)
    {
        $application = $this->getAppManager()
            ->findOneOr404(['appKey' => $appKey]);

        $spotResponse = $this->getSpotOption()->login($email, $password);

        $customer = $this->getEntityManager()
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

        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();

        return $log->getStatus();
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

    /**
     * Get spotOption
     *
     * @return SpotOptionService
     */
    private function getSpotOption()
    {
        if (is_null($this->spotOption)) {
            $this->spotOption = $this->container->get('araneum.agent.spotoption.service');
        }

        return $this->spotOption;
    }

}