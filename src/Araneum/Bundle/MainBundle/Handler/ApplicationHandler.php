<?php

namespace Araneum\Bundle\MainBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplicationHandler
{
    protected $manager;

    protected $entityClass;

    protected $repository;

    /**
     * Class construct
     *
     * @param ObjectManager $manager
     * @param $entityClass
     */
    public function __construct(ObjectManager $manager, $entityClass)
    {
        $this->manager = $manager;
        $this->entityClass = $entityClass;
        $this->repository = $this->manager->getRepository($entityClass);
    }

    /**
     * Get Application
     *
     * @param string $apiKey the application apiKey
     * @return array
     */
    public function get($apiKey)
    {
        $entity = $this->repository->findOneBy(['apiKey' => $apiKey]);

        if (!$entity) {
            throw new NotFoundHttpException('Not Application found for this apiKey');
        }

        $application =
            [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'type' => $entity->getType(),
                'aliases' => $entity->getAliases(),
                'cluster' => $entity->getCluster(),
                'db' => $entity->getDb(),
                'domain' => $entity->getDomain(),
                'public' => $entity->isPublic(),
                'enabled' => $entity->isEnabled(),
                'locale' => $entity->getLocale(),
                'components' => $entity->getComponents(),
                'owner' => $entity->getOwner(),
                'status' => $entity->getStatus(),
                'template' => $entity->getTemplate()
            ];

        return $application;
    }
}