<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RemoteApplicationManagerService
 *
 * @package Araneum\Bundle\MainBundle\Service
 */
class RemoteApplicationManagerService
{
    private $repository;
    private $manager;

    /**
     * Constructor
     *
     * @param $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $manager->getRepository('AraneumMainBundle:Application');
    }

    /**
     * Create application
     *
     * @param Application $appEntity
     * @return mixed
     */
    public function create(Application $appEntity)
    {
        return 222;
    }

    /**
     * Get application
     *
     * @param $appKey
     * @return Application
     */
    public function get($appKey)
    {
        $entity = $this->repository->findOneBy(['appKey' => $appKey]);

        return $entity;
    }

    /**
     * Update application
     *
     * @param mixed
     * @return Application
     */
    public function update($appEntity)
    {
        if ($appEntity instanceof Application) {
            $entity = $appEntity;
        } else {
            $entity = $this->repository->findOneBy(['appKey' => $appEntity]);
        }

        return $entity;
    }

    /**
     * Remove application
     *
     * @param mixed
     * @return bool
     */
    public function remove($appEntity)
    {
        if (is_string($appEntity)) {
            $appEntity = $this->repository->findOneBy(['appKey' => $appEntity]);
        }

        if ($appEntity instanceof Application) {
            //$this->manager->remove($appEntity);
        }

        return true;
    }

    /**
     * Get cluster
     *
     * @param $appKey
     * @return mixed
     */
    public function getClusterByAppKey($appKey)
    {
        return null;
    }
}