<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Response;

class RecursivePagesTest extends BaseController
{
	/**
	 * @var Client.
	 */
	private $client;

	private $register = [];
	private $excludedUrls = [];

	private $success = [
		Response::HTTP_OK,
		Response::HTTP_CREATED,
		Response::HTTP_ACCEPTED,
		Response::HTTP_NON_AUTHORITATIVE_INFORMATION,
		Response::HTTP_NO_CONTENT,
		Response::HTTP_RESET_CONTENT,
		Response::HTTP_PARTIAL_CONTENT,
		Response::HTTP_MULTI_STATUS,
		Response::HTTP_ALREADY_REPORTED,
		Response::HTTP_IM_USED
	];

	/**
	 * @param string|Link $link
	 * @param bool|false $byRequest
	 */
	private function click($link, $byRequest = false)
	{
		$parsedUrl = parse_url($byRequest ? $link : explode("#", $link->getUri())[0]);

		$url = $parsedUrl['path'] . ( ! isset($parsedUrl['query']) ?: '?' . $parsedUrl['query']);

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
			$this->assertTrue(in_array($status, $this->success), $status . "\t" . $url . "\n");
		}
	}
}