<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ApplicationApiHandlerService
 *
 * @package Araneum\Bundle\MainBundle\Service
 */
class ApplicationApiHandlerService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ApplicationRepository
     */
    protected $repository;

    /**
     * Class construct
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get Application Repository
     *
     * @return ApplicationRepository
     */
    public function getRepository()
    {
        if ($this->repository instanceof ApplicationRepository) {
            return $this->repository;
        }

        $this->repository = $this->entityManager->getRepository('AraneumMainBundle:Application');

        return $this->repository;
    }

    /**
     * Get Application
     *
     * @param string $appKey the application appKey
     * @return array
     */
    public function get($appKey)
    {
        $entity = $this->getRepository()->findOneBy(['appKey' => $appKey]);

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
            'template' => $entity->getTemplate(),
        ];

        return $application;
    }
}
