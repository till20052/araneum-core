<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin\Application;

use Araneum\Base\Tests\Controller\BaseAdminController;

class ApplicationAdminTest extends BaseAdminController
{
	protected $createRoute = 'admin_araneum_main_application_create';

	/**
	 * Set of arguments for testCreate method
	 *
	 * @return array
	 */
	public function createDataSource()
	{
		return [
			[
				[
					'name' => 'Test Application',
					'domain' => 'domain.com',
//					'aliases' => ['www.domain.com', 'www2.domain.com'],
					'public' => true,
					'enabled' => true,
					'template' => 'template.html'
				],
				true
			]
		];
	}

	/**
	 * Set of arguments for testFilter method
	 *
	 * @return array
	 */
	public function filterDataSource()
	{
		// TODO: Implement filterDataSource() method.
	}

	/**
	 * Set of arguments for testUpdate method
	 *
	 * @return array
	 */
	public function updateDataSource()
	{
		// TODO: Implement updateDataSource() method.
	}

	/**
	 * Return entity for testDelete method
	 *
	 * @return mixed
	 */
	public function deleteDataSource()
	{
		// TODO: Implement deleteDataSource() method.
	}
}