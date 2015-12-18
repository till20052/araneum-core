<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\Problem;
use Araneum\Bundle\AgentBundle\Service\AgentLoggerService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Repository\ConnectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Service\Client;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class ApplicationCheckerService
 *
 * @package Araneum\Bundle\MainBundle\Service
 */
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
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param Client        $client
     */
    public function __construct(EntityManager $entityManager, Client $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
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
     * Check Connection
     *
     * @param integer $id of Connection
     * @param integer $pingCount
     * @return integer
     *
     * @throws EntityNotFoundException in case if Connection does not exists
     */
    public function checkConnection($id, $pingCount = 5)
    {
        /** @var ConnectionRepository $repository */
        $repository = $this->entityManager->getRepository('AraneumMainBundle:Connection');

        /** @var Connection $connection */
        $connection = $repository->find($id);

        if (empty($connection)) {
            throw new EntityNotFoundException();
        }

        return $this->getConnectionState($connection, $pingCount);
    }

    /**
     * Check Application state
     *
     * @param integer $id of Application
     * @return integer
     *
     * @throws EntityNotFoundException in case if application does not exists
     */
    public function checkApplication($id)
    {
        /** @var ApplicationRepository $repository */
        $repository = $this->entityManager->getRepository('AraneumMainBundle:Application');

        /** @var Application $application */
        $application = $repository->find($id);

        if (empty($application)) {
            throw new EntityNotFoundException();
        }

        return $this->getApplicationState($application);
    }

    /**
     * Check Cluster
     *
     * @param integer $id of Cluster
     * @return integer
     *
     * @throws EntityNotFoundException in case if Cluster does not exists
     */
    public function checkCluster($id)
    {
        /** @var ClusterRepository $repository */
        $repository = $this->entityManager->getRepository('AraneumMainBundle:Cluster');

        /** @var Cluster $cluster */
        $cluster = $repository->find($id);

        if (empty($cluster)) {
            throw new EntityNotFoundException();
        }

        return $this->getClusterState($cluster);
    }

    /**
     * Get State of Connection
     *
     * @param Connection     $connection
     * @param integer        $pingCount
     * @param null|\stdClass $response
     * @return integer
     */
    private function getConnectionState(Connection $connection, $pingCount = 5, &$response = null)
    {
        $status = Connection::STATUS_OK;
        $response = new \stdClass();

        try {
            $process = new Process('ping -c '.$pingCount.' '.$connection->getHost());
            $process->start();
            $process->wait(
                function ($type, $buffer) use ($process, $response) {
                    if ($type == Process::ERR) {
                        throw new ProcessFailedException($process);
                    }

                    if (preg_match(
                        '/(\d+)\spackets\stransmitted,\s(\d+)\sreceived,\s(\d+)%\spacket\sloss,\stime\s(\d+)ms/',
                        $buffer,
                        $match
                    )) {
                        $response->packetsTransmitted = $match[1];
                        $response->received = $match[2];
                        $response->packetLoss = $match[3];
                        $response->time = $match[4];

                        if (preg_match(
                            '/rtt\smin\/avg\/max\/mdev\s=\s([0-9\.]+)\/([0-9\.]+)\/([0-9\.]+)\/([0-9\.]+)\sms/',
                            $buffer,
                            $match
                        )) {
                            $response->min = $match[1];
                            $response->avg = $match[2];
                            $response->max = $match[3];
                            $response->mdev = $match[4];
                        }
                    }
                }
            );

            if ($response->packetsTransmitted == $response->received && $response->avg > 100) {
                $status = Connection::STATUS_SLOW;
            } elseif ($response->packetLoss > 0 && $response->received > 0) {
                $status = Connection::STATUS_HAS_LOSS;
            } elseif ($response->packetLoss == 100) {
                $status = Connection::STATUS_HAS_NO_RESPONSE;
            }
        } catch (\Exception $exception) {
            $status = Connection::STATUS_UNKNOWN_HOST;
        }

        $connection->setStatus($status);
        $this->entityManager->flush();

        return $status;
    }

    /**
     * Get State of Application
     *
     * @param Application $application
     * @return integer
     */
    private function getApplicationState(Application $application)
    {
        $status = Application::STATUS_OK;
        $problems = new ArrayCollection();

        try {
            /** @var GuzzleResponse $request */
            $response = $this->client
                ->get('http'.($application->isUseSsl() ? 's' : '').'://'.$application->getDomain())
                ->send();

            if (!$response->isSuccessful()) {
                $status = Application::STATUS_CODE_INCORRECT;
            }
        } catch (RequestException $e) {
            $status = Application::STATUS_ERROR;
        }

        $application->setStatus($status);
        $this->entityManager->flush();

        if ($status != Application::STATUS_OK) {
            $problem = (new Problem())
                ->setStatus($status)
                ->setDescription(Application::getStatusDescription($status));

            $problems->add($problem);

            $this->loggerService->logApplication($application, $status, $problems);
        }

        return $status;
    }

    /**
     * Get State of Cluster
     *
     * @param Cluster $cluster
     * @return \stdClass
     */
    private function getClusterState(Cluster $cluster)
    {
        $status = Cluster::STATUS_OK;

        $problems = new ArrayCollection();

        /** @var Application $application */
        foreach ($cluster->getApplications() as $application) {
            $applicationStatus = $this->getApplicationState($application);

            if ($applicationStatus != Application::STATUS_OK) {
                $status = Cluster::STATUS_HAS_INCORRECT_APPLICATION;

                $problem = (new Problem())
                    ->setStatus($applicationStatus)
                    ->setDescription(Application::getStatusDescription($applicationStatus));

                $problems->add($problem);
            }
        }

        /** @var Connection $connection */
        foreach ($cluster->getRunners() as $runner) {
            foreach ($runner->getConnection() as $connection) {
                $connectionStatus = $this->getConnectionState($connection, 5, $response);

                $this->loggerService->logConnection(
                    $connection,
                    $runner,
                    $connectionStatus < Connection::STATUS_HAS_NO_RESPONSE ? $response->packetLoss : -1,
                    $connectionStatus < Connection::STATUS_HAS_NO_RESPONSE ? $response->avg : -1
                );

                if ($connectionStatus != Connection::STATUS_OK) {
                    if ($connectionStatus == Connection::STATUS_SLOW) {
                        $status = Cluster::STATUS_HAS_SLOW_CONNECTION;
                    } elseif ($connectionStatus == Connection::STATUS_HAS_LOSS) {
                        $status = Cluster::STATUS_HAS_UNSTABLE_CONNECTION;
                    } elseif ($connectionStatus >= Connection::STATUS_HAS_NO_RESPONSE) {
                        $status = Cluster::STATUS_OFFLINE;
                    }

                    $problem = (new Problem())
                        ->setStatus($connectionStatus)
                        ->setDescription(Connection::getStatusDescription($connectionStatus));
                    $problems->add($problem);
                }
            }
        }

        $cluster->setStatus($status);
        $this->entityManager->flush();

        if ($status != Cluster::STATUS_OK) {
            $this->loggerService->logCluster($cluster, $status, $problems);
        }

        return $status;
    }
}
