<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ClusterFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Entity\Component;

class ClusterApiControllerTest extends BaseController
{
	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var ClusterRepository
	 */
	private $repository;

	/**
	 * Create request and return response from cluster api
	 *
	 * @param string $name Name Of Route
	 * @param array $parameters
	 * @return null|Response
	 */
	private function createRequest($name, $parameters = [])
	{
		$this->client = self::createAdminAuthorizedClient('api');

		$this->client->request(
			'GET',
			$this->client
				->getContainer()
				->get('router')
				->generate($name, $parameters)
		);

		return $this->client->getResponse();
	}

	/**
	 * Get expected structure
	 *
	 * @param Application $application
	 * @return array
	 */
	private function getStructure(Application $application)
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
	 * Initialize requirements
	 */
	protected function setUp()
	{
		$this->client = self::createAdminAuthorizedClient('api');
		$this->repository = $this->client
			->getContainer()
			->get('doctrine.orm.entity_manager')
			->getRepository('AraneumMainBundle:Cluster');
	}

	/**
	 * Test get applications configs by cluster id
	 *
	 * @runInSeparateProcess
	 */
	public function testGetApplicationsConfigsList()
	{
		/** @var Cluster $cluster */
		$cluster = $this->repository->findOneByName(ClusterFixtures::TEST_CLU_NAME);

		$response = $this->createRequest(
			'araneum_main_api_cluster_applications_configs_list',
			[
				'clusterId' => $cluster->getId()
			]
		);

		$statusCode = $response->getStatusCode();

		$this->assertEquals($statusCode, Response::HTTP_OK, 'Status code is not 200');
		$this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

		if($statusCode != Response::HTTP_OK){
			return;
		}

		foreach(json_decode($response->getContent(), true) as $i => $application)
		{
			$this->assertTrue(
				$application === $this->getStructure($cluster->getApplications()[$i]),
				'Structure or value of structure is not equals'
			);
		}
	}

	/**
	 * Test get applications configs list by not existing cluster id
	 *
	 * @runInSeparateProcess
	 */
	public function testGetApplicationsConfigsListByNotExistingCluster()
	{
		$response = $this->createRequest(
			'araneum_main_api_cluster_applications_configs_list',
			[
				'clusterId' => 0
			]
		);

		$this->assertEquals(
			$response->getStatusCode(),
			Response::HTTP_NOT_FOUND,
			'Status code is not 404'
		);
	}
}