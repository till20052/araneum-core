<?php

namespace Araneum\Bundle\MainBundle\Service;

class RemoteApplicationManagerService
{
	/**
	 * Get application data from cluster
	 */
	public function get()
	{

	}

	/**
	 * Create application on cluster
	 *
	 * @param string $appKey
	 * @return bool
	 */
	public function create($appKey)
	{
		return true;
	}

	/**
	 * Update application config in cluster
	 *
	 * @param string $appKey
	 * @return bool
	 */
	public function update($appKey)
	{
		return true;
	}

	/**
	 * Remove application from cluster
	 *
	 * @param string $appKey
	 * @return bool
	 */
	public function remove($appKey)
	{
		return true;
	}
}