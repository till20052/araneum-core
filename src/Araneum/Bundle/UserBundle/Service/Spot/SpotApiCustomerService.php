<?php

namespace Araneum\Bundle\UserBundle\Service\Spot;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\AgentBundle\Service\SpotOptionService;
use Faker\Provider\DateTime;

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
     * @param EntityManager         $em,
     * @param SpotOptionService     $optionService
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
     * @param Application $app
     * @param string $period
     * @return string
     */
    public function getAllCustomersByPeriod($app, $period = 'P1D')
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval($period));
        $this->application = $app;
        $data = $this->optionService->getCustomersByFilter(
            $this->application,
            [
                'regTime' => [
                    'min'=> $date->format('Y-m-d H:i:s')
                ]
            ]
        )->getBody(true);
        $data = json_decode($data, true);
        $result = $data['status'];
        $emails = [];
        $date = [];
        try {
            if (isset($result['errors']) && !empty($result['errors'])) {
                return $result['errors'];
            }
            foreach ($result['Customer'] as $customer) {
                array_push($emails, $customer['email']);
                array_push($date, $customer['birthday']);
            }
            $existingEmails = $this->getExistCustomerEmails($emails);
            foreach ($result['Customer'] as $customer) {
                if (in_array($customer['email'], $existingEmails)) {
                    $this->addSpotCustomer($customer);
                }
            }
            $this->em->flush();
            return true;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage(), $e->getCode()];
        }
    }

    /**
     * Returns emails, exists in Customer entities
     *
     * @param array $emails
     * @return array
     */
    public function getExistCustomerEmails(array $emails)
    {
        $query = $this->em->getRepository('AraneumAgentBundle:Customer')
            ->createQueryBuilder('c')
            ->select("c.email")
            ->where("c.email IN (:emails)")
            ->setParameter('emails', $emails)
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
     * @param array $customerFields
     * @return array
     */
    public function addSpotCustomer(array $customerFields)
    {
        $customer = new Customer();
        if ($customerFields['birthday'] == '0000-00-00') {
            $customerFields['birthday'] = '1990-01-01';
        }
        $customer
                ->setApplication($this->application)
                ->setFirstName($customerFields['FirstName'])
                ->setLastName($customerFields['LastName'])
                ->setEmail($customerFields['email'])
                ->setPhone($customerFields['phone'])
                ->setCurrency($customerFields['currency'])
                ->setBirthday(new \DateTime($customerFields['birthday']))
                ->setSiteId(2)
                ->setCallBack(false)

            ->setCountry(1)
            ->setPassword('123456')
        ;

        $this->em->persist($customer);
    }

}
