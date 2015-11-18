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
     * @var ApplicationRepository
     */
    private $repository;

    /**
     * StatisticsService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository('AraneumMainBundle:Application');
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
        return $this->repository->getApplicationsStatistics();
    }

    /**
     * Get statistics of all applications by statuses last 24 hours
     *
     * -error
     * -problem
     * -ok
     * -disabled
     */
    public function getApplicationsStatusesDayly()
    {
        return $this->repository->getApplicationStatusesDayly();
    }
}