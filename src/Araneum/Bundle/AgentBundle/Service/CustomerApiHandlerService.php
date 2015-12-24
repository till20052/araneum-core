<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\CustomerLog;
use Araneum\Bundle\AgentBundle\Form\Type\CustomerType;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Base\Exception\InvalidFormException;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;

/**
 * Class CustomerApiHandlerService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class CustomerApiHandlerService
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
     * @var SpotOptionService
     */
    protected $spotOption;

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
     * @param  string $appKey the application appKey
     * @param  array  $parameters
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
     * @param  array    $parameters
     * @param  Customer $customer
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

    /**
     * Login Customer
     *
     * @param  string $email
     * @param  string $password
     * @param  string $appKey
     * @return string
     */
    public function login($email, $password, $appKey)
    {
        $application = $this->getAppManager()
            ->findOneOr404(['appKey' => $appKey]);

        $spotResponse = $this->getSpotOption()->login($email, $password);

        $customer = $this->getEntityManager()
            ->getRepository('AraneumAgentBundle:Customer')
            ->findOneBy(['email' => $email]);
        $log = new CustomerLog();
        $log->setApplication($application);
        $log->setAction('Login');
        $log->setCustomer($customer);
        $log->setSpotResponse($spotResponse);

        //TODO respnonse spotoption description
        if ($spotResponse) {
            $log->setStatus(CustomerLog::STATUS_OK);
        } else {
            $log->setStatus(CustomerLog::STATUS_ERROR);
        }

        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();

        return $log->getStatus();
    }

    /**
     * Reset Customer Password
     *
     * @param string $appKey
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     *
     * @throws EntityNotFoundException in case if Application or Customer does not exists
     * @throws \Exception in case if Application does not have Customer
     *
     * @return bool
     */
    public function resetPassword($appKey, $email, $currentPassword, $newPassword)
    {
        /**
         * @var Application $application
         */
        $application = $this->getEntityManager()
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByAppKey($appKey);

        /**
         * @var Customer $customer
         */
        $customer = $this->getEntityManager()
            ->getRepository('AraneumAgentBundle:Customer')
            ->findOneByEmail($email);

        if (empty($application)
            || empty($customer)
        ) {
            throw new EntityNotFoundException();
        } elseif (!$application->getCustomers()->contains($customer)) {
            throw new \Exception('Application does not have this Customer');
        }

        $spotResponse = $this->getSpotOption()
            ->resetPassword($email, $currentPassword, $newPassword);

        $customerLog = (new CustomerLog())
            ->setAction('reset_password')
            ->setApplication($application)
            ->setCustomer($customer)
            ->setSpotResponse($spotResponse)
            ->setStatus(CustomerLog::STATUS_OK);

        $this->getEntityManager()->persist($customerLog);
        $this->getEntityManager()->flush();

        return $customerLog->getStatus();
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
