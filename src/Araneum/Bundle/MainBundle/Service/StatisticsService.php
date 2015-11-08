<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StatisticsService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Get instance of EntityManager
     *
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if ($this->entityManager instanceof EntityManager) {
            return $this->entityManager;
        }

        /** @var EntityManager entityManager */
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');

        return $this->entityManager;
    }

    /**
     * StatisticsService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
        $repository = $this
            ->getEntityManager()
            ->getRepository('AraneumMainBundle:Application');

        return $repository->getApplicationsStatistics();
    }
}