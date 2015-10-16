<?php

namespace Araneum\Bundle\MainBundle\Service;

use Doctrine\ORM\EntityManager;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Entity\Component;

class ClusterApiHandlerService
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var ClusterRepository
	 */
	private $repository;

	/**
	 * Cluster handler constructor
	 *
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Get Cluster Repository
	 *
	 * @return ClusterRepository
	 */
	public function getRepository()
	{
		if($this->repository instanceof ClusterRepository){
			return $this->repository;
		}

		$this->repository = $this->entityManager->getRepository('AraneumMainBundle:Cluster');

		return $this->repository;
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
			'aliases' => $application->getAliases(),
			'template' => $application->getTemplate(),
			'app_key' => $application->getAppKey(),
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
			'locales' => [],
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
		$cluster = $this->getRepository()->find($clusterId);

		if(empty($cluster)){
			return false;
		}

		$list = [];

		/** @var Application $application */
		foreach($cluster->getApplications() as $application)
		{
			$list[] = $this->getApplicationConfigStructure($application);
		}

		return $list;
	}
}