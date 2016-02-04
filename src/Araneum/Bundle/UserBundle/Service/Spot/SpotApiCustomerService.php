<?php

namespace Araneum\Bundle\UserBundle\Service\Spot;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;

/**
 * Class SpotApiCustomerService
 *
 * @package Araneum\Bundle\UserBundle\Service\Spot
 */
class SpotApiCustomerService
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var SpotOptionService
     */
    protected $optionService;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * SpotApiCustomerService constructor.
     *
     * @param EntityManager     $em,
     * @param SpotOptionService $optionService
     */
    public function __construct(
        EntityManager $em,
        SpotOptionService $optionService
    ) {
        $this->em = $em;
        $this->optionService = $optionService;
    }

    /**
     * Get all customers from spot by regTime period
     *
     * @param  Application $application
     * @param  string      $period
     * @return string
     */
    public function getAllCustomersByPeriod($application, $period = 'P1D')
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval($period));

        return  $this->optionService->getCustomersByFilter(
            $application,
            [
                'regTime' => [
                    'min' => $date->format('Y-m-d H:i:s'),
                ],
            ]
        )->getBody(true);
    }

    /**
     * Returns emails, exists in Customer entities
     *
     * @param  array $emails
     * @param  mixed $application
     * @return array
     */
    public function getExistCustomerEmails(array $emails, $application)
    {
        $query = $this->em->getRepository('AraneumAgentBundle:Customer')
            ->createQueryBuilder('c')
            ->select("c.email")
            ->where("c.email IN (:emails) and c.application = :appId")
            ->setParameter('emails', $emails)
            ->setParameter('appId', $application)
            ->getQuery()
            ->getResult();
        $result = [];
        foreach ($query as $existEmail) {
            $result[] = $existEmail['email'];
        }

        return array_diff($emails, $result);
    }

    /**
     * Returns emails, exists in Customer entities
     *
     * @param  array       $customerFields
     * @param  Application $application
     * @return array
     */
    public function addSpotCustomer(array $customerFields, $application)
    {
        $customer = new Customer();
        if ($customerFields['birthday'] == '0000-00-00') {
            $customerFields['birthday'] = '1990-01-01';
        }
        $country = $this->em->getRepository('AraneumAgentBundle:Country')->findOneByTitle($customerFields['Country']);
        $customer
            ->setApplication($application)
            ->setFirstName($customerFields['FirstName'])
            ->setLastName($customerFields['LastName'])
            ->setEmail($customerFields['email'])
            ->setPhone($customerFields['phone'])
            ->setCurrency($customerFields['currency'])
            ->setBirthday(new \DateTime($customerFields['birthday']))
            ->setSiteId(2)
            ->setCallBack(false)
            ->setEnableSite(false)
            ->setCountry($country->getId())
            ->setPassword($customerFields['password']);

        $this->em->persist($customer);
    }
}
