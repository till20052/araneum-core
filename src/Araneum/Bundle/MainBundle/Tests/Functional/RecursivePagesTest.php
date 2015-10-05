<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Link;

class RecursivePagesTest extends BaseController
{
	/**
	 * @var Client.
	 */
	private $client;
	private $statuses = [200, 201, 202, 203, 204, 205, 206, 207, 226];
	private $register = [];
	private $excludedUrls = [];

	/**
	 * @param string|Link $link
	 * @param bool|false $byRequest
	 */
	private function click($link, $byRequest = false)
	{
		$url = $byRequest ? $link : explode("#", $link->getUri())[0];

		if(isset($this->register[$url]) || in_array($url, $this->excludedUrls))
			return;

		$crawler = $byRequest
			? $this->client->request('GET', $url)
			: $this->client->click($link);

		$this->register[$url] = $this->client->getResponse()->getStatusCode();

		foreach($crawler->filter('a')->links() as $token)
		{
			$this->click($token);
		}
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testPages()
	{
		$this->client = $this->createAdminAuthorizedClient();

		$container = $this->client->getContainer();

		$router = $container->get('router');

		foreach(explode('|', $container->getParameter('locales')) as $locale)
		{
			$this->excludedUrls[] = $router->generate('fos_user_security_logout', ['_locale' => $locale]);
		}

		$this->click($router->generate('sonata_admin_dashboard'), true);

		foreach($this->register as $url => $status)
		{
			echo $url, "\t", $url, "\n";
		}
	}
}