<?php

namespace Araneum\Bundle\MainBundle\Handler;

use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Entity\Component;

class ClusterHandler
{
	/**
	 * @var ClusterRepository
	 */
	private $repository;

	/**
	 * Cluster handler constructor
	 *
	 * @param EntityManager $entityManager
	 * @param string $clusterClass
	 */
	public function __construct(EntityManager $entityManager, $clusterClass)
	{
		$this->repository = $entityManager->getRepository($clusterClass);
	}

	/**
	 * Get configurations list of applications which cluster contains
	 *
	 * @param $clusterId
	 * @return array|bool
	 */
	public function getApplicationsConfigsList($clusterId)
	{
		/** @var Cluster $cluster */
		$cluster = $this->repository->find($clusterId);

		if(empty($cluster))
			return false;

		$list = [];

		/** @var Application $application */
		foreach($cluster->getApplications() as $application)
		{
			$token = [
				'domain' => $application->getDomain(),
				'template' => $application->getTemplate(),
				'aliases' => $application->getAliases(),
				'app_key' => $application->getApiKey(),
				'cluster' => [
					'id' => $cluster->getId()
				],
				'db' => [
					'name' => $application->getDb()->getName(),
					'host' => $application->getDb()->getHost(),
					'port' => $application->getDb()->getPort(),
					'user_name' => $application->getDb()->getUserName(),
					'password' => $application->getDb()->getPassword(),
				],
				'locale' => [],
				'components' => []
			];

			/** @var Locale $locale */
			foreach($application->getLocales() as $locale)
			{
				$token['locales'][] = [
					'name' => $locale->getName(),
					'locale' => $locale->getLocale(),
					'orientation' => $locale->getOrientation(),
					'encoding' => $locale->getEncoding()
				];
			}

			/** @var Component $component */
			foreach($application->getComponents() as $component)
			{
				$token['components'][] = [
					'name' => $component->getName(),
					'options' => $component->getOptions()
				];
			}

			$list[] = $token;
		}

		return $list;
	}
}