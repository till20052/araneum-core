<?php

namespace Araneum\Bundle\MainBundle\Event;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Symfony\Component\EventDispatcher\Event;

class ClusterEvent extends Event implements AdminEventInterface
{
	/**
	 * @var Cluster
	 */
	private $cluster;

	/**
	 * Constructor of Cluster Event
	 *
	 * @param Cluster $cluster
	 */
	public function __construct(Cluster $cluster)
	{
		$this->cluster = $cluster;
	}

	/**
	 * Get cluster applications
	 *
	 * @return array
	 */
	public function getApplications()
	{
		return $this->cluster->getApplications()->toArray();
	}
}