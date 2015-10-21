<?php

namespace Araneum\Bundle\MainBundle\Service;

use Doctrine\ORM\EntityManager;

class ApplicationCheckerService
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * Constructor
	 *
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager, $guzzle)
	{
		var_dump($guzzle);die;

		$this->entityManager = $entityManager;
	}

	/**
	 * Check Connection state
	 *
	 * @param $id
	 */
	public function checkConnection($id)
	{

	}

	/**
	 * Check Cluster state
	 *
	 * @param $id
	 */
	public function checkCluster($id)
	{

	}

	/**
	 * Check Application state
	 *
	 * @param $id
	 */
	public function checkApplication($id)
	{

	}
}