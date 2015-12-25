<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\RabbitMQ\SpotProducerService;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Base\Service\Spot\SpotApiSenderService;
use Doctrine\ORM\EntityManager;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionService
{
    /**
     * @var SpotProducerService
     */
    protected $spotProducerService;

    /**
     * @var SpotOptionService
     */
    protected $spotApiSenderService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * SpotOptionService constructor.
     *
     * @param SpotProducerService  $spotProducerService
     * @param SpotApiSenderService $spotApiSenderService
     * @param EntityManager        $entityManager
     */
    public function __construct(
        SpotProducerService $spotProducerService,
        SpotApiSenderService $spotApiSenderService,
        EntityManager $entityManager
    ) {
        $this->spotProducerService = $spotProducerService;
        $this->spotApiSenderService = $spotApiSenderService;
        $this->entityManager = $entityManager;
    }

    /**
     * SpotOption Login
     *
     * @param  string $login
     * @param  string $password
     * @return bool
     */
    public function login($login, $password)
    {
        $login = null;
        $password = null;

        return true;
    }

    /**
     * Reset Customer Password on SpotOption
     *
     * @param  string $login
     * @param  string $currentPassword
     * @param  string $newPassword
     * @return bool
     */
    public function resetPassword($login, $currentPassword, $newPassword)
    {
        $login = null;
        $currentPassword = null;
        $newPassword = null;

        return true;
    }

    /**
     * Send customer creation data to SpotOption with RabbitMQ
     *
     * @param Customer $customer
     * @return string|true
     */
    public function customerCreate(Customer $customer)
    {
        $customerData = [
            'MODULE' => 'Customer',
            'COMMAND' => 'add',
            'FirstName' => $customer->getFirstName(),
            'LastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'password' => $customer->getPassword(),
            'Phone' => $customer->getPhone(),
            'Country' => $customer->getCountry(),
            'currency' => $customer->getCurrency(),
        ];

        if ($customer->getBirthday()) {
            $customerData['birthday'] = $customer->getBirthday()->format('Y-m-d');
        }

        return $this->spotProducerService->publish($customerData, $customer->getApplication());
    }

    /**
     * Get countries method
     *
     * @param string $appKey
     * @return mixed
     */
    public function getCountries($appKey)
    {
        $repository = $this->entityManager->getRepository('AraneumMainBundle:Application');
        $entity = $repository->findOneBy(['appKey' => $appKey]);

        $spotCredential = $entity->getSpotCredential();

        $data = [
            'MODULE' => 'Country',
            'COMMAND' => 'view',
        ];

        return $this->spotApiSenderService->get($data, $spotCredential);
    }
}
