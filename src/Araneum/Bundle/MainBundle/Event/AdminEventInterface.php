<?php

namespace Araneum\Bundle\MainBundle\Event;

interface AdminEventInterface
{
	/**
	 * Get applications
	 *
	 * @return array
	 */
	public function getApplications();
}