<?php

namespace Araneum\Bundle\UserBundle\Service\Spot;

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
     * @param string $period
     * @return string
     */
    public function getAllCustomersByPeriod($period = '1H', $app)
    {
        $this->application = $app;
        $data = $this->optionService->getCustomersByFilter(
            $this->application,
            [
                'regTime' => [
                    'min'=> '2016-01-01'
                ]
            ]
        )->getBody(true);
        $data = json_decode($data, true);
        $answ = [];
        foreach ($data['status']['Customer'] as $customer) {
            $answ [$customer['id']] = $customer['email'].' '.$customer['regTime'].' '.$customer['Country'];
        }
        return $answ;
    }

}
