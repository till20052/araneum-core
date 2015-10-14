<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class ApplicationManager
{
    /** @var  Registry */
    private $doctrine;
    private $repository;

    /**
     * ApplicationManager constructor
     *
     * @param $doctrine
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Find entity or throw NotFoundHttpException
     *
     * @param array $criteria
     * @throws NotFoundHttpException
     * @return null|Application
     */
    public function findOneOr404(array $criteria)
    {
        $entity = $this->getRepository()->findOneBy($criteria);
        if (empty($entity)) {
            throw new NotFoundHttpException('Not Application found for this appKey', null, Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    /**
     *  Get Repository
     *
     * @return \Araneum\Bundle\MainBundle\Repository\ApplicationRepository
     */
    public function getRepository()
    {
        if ($this->repository) {
            return $this->repository;
        }

        $this->repository = $this->doctrine->getRepository('AraneumMainBundle:Application');

        return $this->repository;
    }
}