<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;

class ApiApplicationManager
{
    private $repository;

    public function __construct($doctrine)
    {
        $this->repository = $doctrine->getRepository('AraneumMainBundle:Application');
    }

    public function createApplication()
    {
        $entity = $this->repository->findOneByName(ApplicationFixtures::TEST_APP_NAME);

        return $entity;
    }

    public function getApplication($appKey)
    {
        $entity = $this->repository->findOneBy(['app_key' => $appKey]);

        return $entity;
    }

    public function updateApplication($appKey)
    {
        $entity = $this->repository->findOneBy(['app_key' => $appKey]);

        return $entity;
    }

    public function deleteApplication($appKey)
    {
        return true;
    }

    public function getClusterByAppKey($appKey)
    {
    }

}