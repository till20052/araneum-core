<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\AgentBundle\Service\AgentLoggerService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Repository\ConnectionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Service\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ApplicationCheckerService
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var AgentLoggerService
	 */
	private $loggerService;

	/**
	 * @var \stdClass
	 */
	private $output;

	/**
	 * Check Connection State
	 *
	 * @param $id
	 * @param int $pingCount
	 * @return \stdClass
	 *
	 * @throws ProcessFailedException in case if process of ping has errors
	 * @throws EntityNotFoundException in case if Connection does not exists
	 */
	private function checkConnectionState($id, $pingCount = 5)
	{
		$state = new \stdClass();

		/** @var ConnectionRepository $repository */
		$repository = $this->entityManager->getRepository('AraneumMainBundle:Connection');

		/** @var Connection $connection */
		$connection = $repository->find($id);

		if (empty($connection)) {
			throw new EntityNotFoundException();
		}

		$process = new Process('ping -c ' . $pingCount . ' ' . $connection->getHost());
		$process->start();

		$process->wait(
			function ($type, $buffer) use ($process, $state) {
				if (Process::ERR === $type) {
					throw new ProcessFailedException($process);
				}

				if (preg_match(
				/** @TODO Need to define list of ping stdout patterns */
					'/(\d+)\spackets\stransmitted,\s(\d+)\sreceived,\s(\d+)%\spacket\sloss,\stime\s(\d+)ms\n'
					. 'rtt\smin\/avg\/max\/mdev\s=\s([0-9\.]+)\/([0-9\.]+)\/([0-9\.]+)\/([0-9\.]+)\sms/',
					$buffer,
					$match
				)) {
					$state->packetsTransmitted = $match[1];
					$state->received = $match[2];
					$state->packetLoss = $match[3];
					$state->time = $match[4];
					$state->min = $match[5];
					$state->avg = $match[6];
					$state->max = $match[7];
					$state->mdev = $match[8];
				};
			}
		);

		$connection->setStatus(isset($state->received) ? $state->received > 0 : false);
		$this->entityManager->flush();

		$state->connection = $connection;

		return $state;
	}

	/**
	 * Check Cluster State
	 *
	 * @param $id
	 *
	 * @throws EntityNotFoundException in case if Cluster does not exists
	 */
	private function checkClusterState($id)
	{
		/** @var ClusterRepository $repository */
		$repository = $this->entityManager->getRepository('AraneumMainBundle:Cluster');

		/** @var Cluster $cluster */
		$cluster = $repository->find($id);

		if(empty($cluster)){
			throw new EntityNotFoundException();
		}

		/** @var Connection $connection */
		foreach ($cluster->getHosts() as $connection) {
			/** @var \stdClass $state */
			$state = $this->checkConnectionState($connection->getId());

			$this->loggerService->logConnection(
				$connection,
				$cluster,
				$state->packetLoss,
				$state->avg * 1000
			);
		}

		$appStatusFalse = [];
		$appStatusTrue = [];

		/** @var Application $application */
		foreach ($cluster->getApplications() as $application) {
			if (!$this->checkApplication($application->getId())) {
				$appStatusFalse[] = $application->getId();
			} else {
				$appStatusTrue[] = $application->getId();
			}
		}

		$status = Cluster::STATUS_OFFLINE;
		$this->output->statusDescription = 'offline';

		if (
			count($appStatusFalse) == 0
			&& count($appStatusTrue) == $cluster->getApplications()->count()
		) {
			$status = Cluster::STATUS_ONLINE;
			$this->output->statusDescription = 'online';
		} elseif (count($appStatusFalse) > 0 && count($appStatusTrue) > 0) {
			$status = Cluster::STATUS_HAS_PROBLEMS;
			$this->output->statusDescription = 'has problems';
		}

		$cluster->setStatus($status);
		$this->entityManager->flush();
	}

	/**
	 * Constructor
	 *
	 * @param EntityManager $entityManager
	 * @param Client $client
	 */
	public function __construct(EntityManager $entityManager, Client $client)
	{
		$this->entityManager = $entityManager;
		$this->client = $client;
		$this->output = new \stdClass();
	}

	/**
	 * Set Agent Logger Service
	 *
	 * @param AgentLoggerService $loggerService
	 */
	public function setAgentLogger(AgentLoggerService $loggerService)
	{
		$this->loggerService = $loggerService;
	}

	/**
	 * Get operation output
	 *
	 * @return \stdClass
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * Check Connection state
	 *
	 * @param $id
	 * @param int $pingCount by default 5
	 * @return mixed
	 */
	public function checkConnection($id, $pingCount = 5)
	{
		/** @var \stdClass output */
		$this->output = $this->checkConnectionState($id, $pingCount);

		/** @var Connection $connection */
		$connection = $this->output->connection;
		unset($this->output->connection);

		return $connection->getStatus();
	}

	/**
	 * Check Application state
	 *
	 * @param $id
	 * @return bool
	 */
	public function checkApplication($id)
	{
		$this->output = new \stdClass();

		/** @var ApplicationRepository $repository */
		$repository = $this->entityManager->getRepository('AraneumMainBundle:Application');

		/** @var Application $application */
		$application = $repository->find($id);

		$status = false;

		try {
			/** @var GuzzleResponse $request */
			$response = $this->client
				->get('http' . ($application->isUseSsl() ? 's' : '') . '://' . $application->getDomain())
				->send();

			$status = in_array(
				$response->getStatusCode(),
				range(Response::HTTP_OK, Response::HTTP_MULTI_STATUS) + [Response::HTTP_IM_USED]
			);
		} catch (CurlException $e) {
			/** @TODO Need to define list of errors and connect it with statuses of application */
		}

		$application->setStatus($status);
		$this->entityManager->flush();

		return $application->getStatus();
	}

	/**
	 * Check Cluster state
	 *
	 * @param $id
	 * @return boolean
	 */
	public function checkCluster($id)
	{
		$this->output = new \stdClass();

		/** @var ClusterRepository $repository */
		$repository = $this->entityManager->getRepository('AraneumMainBundle:Cluster');

		/** @var Cluster $cluster */
		$cluster = $repository->find($id);

		/** @var Connection $connection */
		foreach ($cluster->getHosts() as $connection) {
			/** @var \stdClass $state */
			$state = $this->checkConnectionState($connection->getId());

			$this->loggerService->logConnection(
				$connection,
				$cluster,
				$state->packetLoss,
				$state->avg * 1000
			);
		}

		$appStatusFalse = [];
		$appStatusTrue = [];

		/** @var Application $application */
		foreach ($cluster->getApplications() as $application) {
			if (!$this->checkApplication($application->getId())) {
				$appStatusFalse[] = $application->getId();
			} else {
				$appStatusTrue[] = $application->getId();
			}
		}

		$status = Cluster::STATUS_OFFLINE;
		$this->output->statusDescription = 'offline';

		if (
			count($appStatusFalse) == 0
			&& count($appStatusTrue) == $cluster->getApplications()->count()
		) {
			$status = Cluster::STATUS_ONLINE;
			$this->output->statusDescription = 'online';
		} elseif (count($appStatusFalse) > 0 && count($appStatusTrue) > 0) {
			$status = Cluster::STATUS_HAS_PROBLEMS;
			$this->output->statusDescription = 'has problems';
		}

		$cluster->setStatus($status);
		$this->entityManager->flush();

		return $cluster->getStatus();
	}
}