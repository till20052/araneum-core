<?php

namespace Araneum\Bundle\MainBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplicationHandlerService
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
     * @param string $appKey the application appKey
     * @return array
     */
    public function get($appKey)
    {
        $entity = $this->repository->findOneBy(['appKey' => $appKey]);

        if (!$entity) {
            throw new NotFoundHttpException('Not Application found for this appKey', null, Response::HTTP_NOT_FOUND);
        }

        $application = [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'type' => $entity->getType(),
            'aliases' => $entity->getAliases(),
            'cluster' => $entity->getCluster(),
            'db' => $entity->getDb(),
            'domain' => $entity->getDomain(),
            'public' => $entity->isPublic(),
            'enabled' => $entity->isEnabled(),
            'locales' => $entity->getLocales(),
            'components' => $entity->getComponents(),
            'owner' => $entity->getOwner(),
            'status' => $entity->getStatus(),
            'template' => $entity->getTemplate()
        ];

        return $application;
    }
}