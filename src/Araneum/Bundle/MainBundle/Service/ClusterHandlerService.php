<?php

namespace Araneum\Bundle\MainBundle\Service;

use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Entity\Component;

class ClusterHandlerService
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
	 * Get application config structure
	 *
	 * @param Application $application
	 * @return array
	 */
	public function getApplicationConfigStructure(Application $application)
	{
		$structure = [
			'domain' => $application->getDomain(),
			'template' => $application->getTemplate(),
			'aliases' => $application->getAliases(),
			'app_key' => $application->getApiKey(),
			'cluster' => [
				'id' => $application->getCluster()->getId()
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
			$structure['locales'][] = [
				'name' => $locale->getName(),
				'locale' => $locale->getLocale(),
				'orientation' => $locale->getOrientation(),
				'encoding' => $locale->getEncoding()
			];
		}

		/** @var Component $component */
		foreach($application->getComponents() as $component)
		{
			$structure['components'][] = [
				'name' => $component->getName(),
				'options' => $component->getOptions()
			];
		}

		return $structure;
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
			$list[] = $this->getApplicationConfigStructure($application);
		}

		return $list;
	}
}