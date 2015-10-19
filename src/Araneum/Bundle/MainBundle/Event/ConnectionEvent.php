<?php

namespace Araneum\Bundle\MainBundle\Event;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Symfony\Component\EventDispatcher\Event;

class ConnectionEvent extends Event implements AdminEventInterface
{
	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * Constructor of Connection Event
	 *
	 * @param Connection $connection
	 */
	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Get connection applications
	 *
	 * @return array
	 */
	public function getApplications()
	{
		$applications = [];

		/** @var Cluster $cluster */
		foreach($this->connection->getClusters() as $cluster){
			$applications = $applications + $cluster->getApplications()->toArray();
		}

		return $applications;
	}
}