<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ClusterFixtures;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Service\ClusterApiHandlerService;

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
	 * @var ClusterApiHandlerService
	 */
	private $handler;

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
	 * Initialize requirements
	 */
	protected function setUp()
	{
		$this->client = self::createAdminAuthorizedClient('api');
		$container = $this->client->getContainer();
		$this->handler = $container->get('araneum.main.cluster.handler');
		$this->repository = $container
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
		$content = $response->getContent();

		$this->assertEquals(
			Response::HTTP_OK,
			$response->getStatusCode(),
			$content
		);
		$this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

		foreach(json_decode($content, true) as $i => $application)
		{
			$this->assertTrue(
				$application === $this->handler->getApplicationConfigStructure($cluster->getApplications()[$i]),
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
			Response::HTTP_NOT_FOUND,
			$response->getStatusCode()
		);
	}
}