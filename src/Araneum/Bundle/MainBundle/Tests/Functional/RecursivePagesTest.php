<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Araneum\Base\Tests\Controller\BaseController;

class RecursivePagesTest extends BaseController
{
	private $register = [];

	/**
	 * @runInSeparateProcess
	 */
	public function testPages()
	{
		$client = $this->createAdminAuthorizedClient();

		$crawler = $client
			->request('GET',
				$client
					->getContainer()
					->get('router')
					->generate('sonata_admin_dashboard')
			);

		$list = $crawler->filter('a')->each(function($node){
			return $node->link();
		});

		$titles = [];
		foreach($list as $link)
		{
			$crawler = $client->click($link);

			$titles[] = trim($crawler->filter('head > title')->text());
		}

		var_dump($titles);die;
	}
}