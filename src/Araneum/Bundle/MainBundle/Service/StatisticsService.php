<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StatisticsService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * StatisticsService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get statistics of all applications by next conditions:
     *  - online
     *  - has problems
     *  - has errors
     *  - disabled
     *
     * @return \stdClass
     */
    public function getApplicationsStatistics()
    {
        /** @var ApplicationRepository $repository */
        $repository = $this->entityManager
            ->getRepository('AraneumMainBundle:Application');

        return $repository->getApplicationsStatistics();
    }
}