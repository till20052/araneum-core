<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Repository\ConnectionRepository;
use Doctrine\ORM\EntityManager;
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
	 * Constructor
	 *
	 * @param EntityManager $entityManager
	 * @param Client $client
	 */
	public function __construct(EntityManager $entityManager, Client $client)
	{
		$this->entityManager = $entityManager;
		$this->client = $client;
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
		/** @var ConnectionRepository $repository */
		$repository = $this->entityManager->getRepository('AraneumMainBundle:Connection');

		/** @var Connection $connection */
		$connection = $repository->find($id);

		$process = new Process('ping -c '.$pingCount.' '.$connection->getHost());
		$process->start();

		$output = new \stdClass();
		$process->wait(
			function($type, $buffer) use ($process, $output) {
				if (Process::ERR === $type) {
					throw new ProcessFailedException($process);
				}

				if(preg_match(
					/** @TODO Need to define list of ping stdout patterns */
					'/(\d+)\spackets\stransmitted,\s(\d+)\sreceived,\s(\d+)%\spacket\sloss,\stime\s(\d+)ms/',
					$buffer,
					$match
				)){
					$output->packetsTransmitted = $match[1];
					$output->received = $match[2];
					$output->packetLoss = $match[3];
					$output->time = $match[4];
				};
			}
		);

		$connection->setStatus(isset($output->received) ? $output->received > 0 : false);
		$this->entityManager->flush();

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
		/** @var ApplicationRepository $repository */
		$repository = $this->entityManager->getRepository('AraneumMainBundle:Application');

		/** @var Application $application */
		$application = $repository->find($id);

		$status = false;

		try
		{
			/** @var GuzzleResponse $request */
			$response = $this->client
				->get('http'.($application->isUseSsl() ? 's' : '').'://'.$application->getDomain())
				->send();

			$status = in_array(
				$response->getStatusCode(),
				range(Response::HTTP_OK, Response::HTTP_MULTI_STATUS) + [Response::HTTP_IM_USED]
			);
		}
		catch(CurlException $e)
		{
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
		/** @var ClusterRepository $repository */
		$repository = $this->entityManager->getRepository('AraneumMainBundle:Cluster');

		/** @var Cluster $cluster */
		$cluster = $repository->find($id);

		$appStatusFalse = [];
		$appStatusTrue = [];

		/** @var Application $application */
		foreach($cluster->getApplications() as $application)
		{
			if( ! $this->checkApplication($application->getId())){
				$appStatusFalse[] = $application->getId();
			}
			else {
				$appStatusTrue[] = $application->getId();
			}
		}

		$status = Cluster::STATUS_OFFLINE;
		if(
			count($appStatusFalse) == 0
			&& count($appStatusTrue) == $cluster->getApplications()->count()
		){
			$status = Cluster::STATUS_ONLINE;
		}
		elseif(count($appStatusFalse) > 0 && count($appStatusTrue) > 0){
			$status = Cluster::STATUS_HAS_PROBLEMS;
		}

		$cluster->setStatus($status);
		$this->entityManager->flush();

		return $cluster->getStatus();
	}
}