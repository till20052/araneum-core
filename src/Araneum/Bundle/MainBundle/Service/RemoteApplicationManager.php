<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Persistence\ObjectManager;

class RemoteApplicationManager
{
    private $repository;
    private $manager;


    /**
     * Constructor
     *
     * @param $doctrine
     */
    public function __construct(ObjectManager $doctrine)
    {
        $this->manager = $doctrine;
        $this->repository = $doctrine->getRepository('AraneumMainBundle:Application');
    }

    /**
     * Create application
     *
     * @param
     * @return Application
     */
    public function create($appEntity)
    {
        $this->manager->persist($appEntity);
        $this->manager->flush();

        return $appEntity;
    }

    /**
     * Get application
     *
     * @param $appKey
     * @return Application
     */
    public function get($appKey)
    {
        $entity = $this->repository->findOneBy(['app_key' => $appKey]);

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
            $entity = $this->repository->findOneBy(['app_key' => $appEntity]);
        }

        return $entity;
    }

    /**
     * Delete application
     *
     * @param mixed
     * @return bool
     */
    public function deleteApplication($appEntity)
    {
        if (is_string($appEntity)) {
            $appEntity = $this->repository->findOneBy(['app_key' => $appEntity]);
        }

        if ($appEntity instanceof Application) {
            $this->manager->remove($appEntity);
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