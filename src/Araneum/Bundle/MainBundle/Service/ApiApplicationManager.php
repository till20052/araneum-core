<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;

class ApiApplicationManager
{
    private $repository;

    /**
     * Constructor
     *
     * @param $doctrine
     */
    public function __construct($doctrine)
    {
        $this->repository = $doctrine->getRepository('AraneumMainBundle:Application');
    }

    /**
     * Create application
     *
     * @return Application
     */
    public function createApplication()
    {
        $entity = $this->repository->findOneByName(ApplicationFixtures::TEST_APP_NAME);

        return $entity;
    }

    /**
     * Get application
     *
     * @param $appKey
     * @return Application
     */
    public function getApplication($appKey)
    {
        $entity = $this->repository->findOneBy(['app_key' => $appKey]);

        return $entity;
    }

    /**
     * Update application
     *
     * @param $appKey
     * @return Application
     */
    public function updateApplication($appKey)
    {
        $entity = $this->repository->findOneBy(['app_key' => $appKey]);

        return $entity;
    }

    /**
     * Delete application
     *
     * @param $appKey
     * @return bool
     */
    public function deleteApplication($appKey)
    {
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