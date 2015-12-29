<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Bundle\AgentBundle\AraneumAgentBundle;
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
use Araneum\Bundle\AgentBundle\Event\CustomerEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CustomerApiHandlerService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class CustomerApiHandlerService
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
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
    protected $applicationManager;
    /**
     * @var SpotOptionService
     */
    protected $spotOptionService;
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * Service Constructor
     *
     * @param ApplicationManagerService $applicationManager
     * @param EntityManager             $entityManager
     * @param EventDispatcherInterface  $dispatcher
     * @param FormFactory               $formFactory
     * @param SpotOptionService         $spotOptionService
     */
    public function __construct(
        ApplicationManagerService $applicationManager,
        EntityManager $entityManager,
        EventDispatcherInterface $dispatcher,
        FormFactory $formFactory,
        SpotOptionService $spotOptionService
    ) {
        $this->applicationManager = $applicationManager;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->formFactory = $formFactory;
        $this->spotOptionService = $spotOptionService;
    }

    /**
     * Get Customer
     *
     * @param string $appKey
     * @param array  $parameters
     * @return array
     */
    public function post($appKey, array $parameters)
    {
        $application = $this->applicationManager->findOneOr404(['appKey' => $appKey]);

        $customer = new Customer();
        $customer->setApplication($application);

        $result = $this->processForm($parameters, $customer);

        $this->createCustomerEvent($customer, AraneumAgentBundle::EVENT_CUSTOMER_NEW);

        return $result;
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
        $form = $this->formFactory->create(new CustomerType(), $customer);
        $form->submit($parameters);

        if ($form->isValid()) {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return ['id' => $customer->getId()];
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
        $email = strtolower($email);
        
        $customer = $this->validateCustomerAndApplication($appKey, $email);
        $spotResponse = $this->spotOptionService->login($email, $password);
        $this->createCustomerLog(CustomerLog::ACTION_LOGIN, $customer, $spotResponse);

        return 0;
    }
    /**
     * Reset Customer Password
     *
     * @param string $appKey
     * @param string $email
     * @param int    $customerId
     * @param string $password
     *
     * @throws EntityNotFoundException in case if Application or Customer does not exists
     * @throws \Exception in case if Application does not have Customer
     *
     * @return bool
     */
    public function resetPassword($appKey, $email, $customerId, $password)
    {
        $email = strtolower($email);
        
        $customer = $this->validateCustomerAndApplication($appKey, $email);
        $customer
            ->setPassword($password)
            ->setSpotId($customerId);
        $this->createCustomerEvent($customer, AraneumAgentBundle::EVENT_CUSTOMER_RESET_PASSWORD);

        return 'successful';
    }

    /**
     * Create and save customer log
     *
     * @param string   $actionName
     * @param Customer $customer
     * @param string   $spotResponse
     * @return CustomerLog
     */
    private function createCustomerLog($actionName, Customer $customer, $spotResponse)
    {
        $logStatus = CustomerLog::STATUS_ERROR;
        if ($spotResponse) {
            $logStatus = CustomerLog::STATUS_OK;
        }

        $customerLog = (new CustomerLog())
            ->setAction($actionName)
            ->setApplication($customer->getApplication())
            ->setCustomer($customer)
            ->setSpotResponse($spotResponse)
            ->setStatus($logStatus);

        $this->entityManager->persist($customerLog);
        $this->entityManager->flush();

        return $customerLog;
    }

    /**
     * Validate customer and application, if all is return customer
     *
     * @param $appKey
     * @param $email
     * @return Customer
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    private function validateCustomerAndApplication($appKey, $email)
    {
        /** @var Application $application */
        $application = $this->applicationManager->findOneOr404(['appKey' => $appKey]);
        /** @var Customer $customer */
        $customer = $this->entityManager->getRepository('AraneumAgentBundle:Customer')->findOneBy(['email' => $email]);

        if (empty($customer)) {
            throw new EntityNotFoundException('Not Customer found for this $email: '.$email);
        }

        if (!$application->getCustomers()->contains($customer)) {
            throw new \Exception("Application does not have this Customer. customer.id=".$customer->getId());
        }

        return $customer;
    }

    /**
     * Create and dispatch Customer event
     *
     * @param Customer $customer
     * @param string   $eventName
     */
    private function createCustomerEvent(Customer $customer, $eventName)
    {
        $event = new CustomerEvent();
        $event->setCustomer($customer);
        $this->dispatcher->dispatch($eventName, $event);
    }
}
