<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin\Component;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class ComponentAdminListTest extends BaseController
{
	/**
	 * Test list
	 *
	 * @runInSeparateProcess
	 */
	public function testList()
	{
		$client = $this->createAdminAuthorizedClient();

		$client->request('GET',
			$client
				->getContainer()
				->get('router')
				->generate('admin_araneum_main_component_list')
		);

		$this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
	}
}