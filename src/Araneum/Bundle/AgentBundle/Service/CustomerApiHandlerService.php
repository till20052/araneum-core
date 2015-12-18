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
     *
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
     * @param string $appKey     the application appKey
     * @param array  $parameters
     * @return array
     */
    public function post($appKey, array $parameters)
    {
        $application = $this->applicationManager->findOneOr404(['appKey' => $appKey]);

        $customer = new Customer();
        $customer->setApplication($application);

        $result = $this->processForm($parameters, $customer);

        $event = new CustomerEvent();
        $event->setCustomer($customer);
        $this->dispatcher->dispatch(AraneumAgentBundle::EVENT_CUSTOMER_NEW, $event);

        return $result;
    }

    /**
     * Process Form
     *
     * @param array    $parameters
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

            return ['id' => $customer->getId()];
        } else {
            throw new InvalidFormException($form, 'Invalid submitted data');
        }
    }

    /**
     * Login Customer
     *
     * @param string $email
     * @param string $password
     * @param string $appKey
     * @return string
     */
    public function login($email, $password, $appKey)
    {
        $application = $this->applicationManager->findOneOr404(['appKey' => $appKey]);

        $spotResponse = $this->spotOptionService->login($email, $password);

        $customer = $this->entityManager
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

        $this->entityManager->persist($log);
        $this->entityManager->flush();

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
        /** @var Application $application */
        $application = $this->entityManager
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByAppKey($appKey);

        /** @var Customer $customer */
        $customer = $this->entityManager
            ->getRepository('AraneumAgentBundle:Customer')
            ->findOneByEmail($email);

        if (empty($application) || empty($customer)) {
            throw new EntityNotFoundException();
        } elseif (!$application->getCustomers()->contains($customer)) {
            throw new \Exception('Application does not have this Customer');
        }

        $spotResponse = $this->spotOptionService
            ->resetPassword($email, $currentPassword, $newPassword);

        $customerLog = (new CustomerLog())
            ->setAction('reset_password')
            ->setApplication($application)
            ->setCustomer($customer)
            ->setSpotResponse($spotResponse)
            ->setStatus(CustomerLog::STATUS_OK);

        $this->entityManager->persist($customerLog);
        $this->entityManager->flush();

        return $customerLog->getStatus();
    }
}
