<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

class RecursivePagesTest extends BaseController
{
	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var Router
	 */
	private $router;

	private $register = [];
	private $excludedUrls = [];

	/**
	 * Prepare url
	 *
	 * @param $url
	 * @return string
	 */
	private function prepareUrl($url)
	{
		$parsedUrl = parse_url($url);

		return $parsedUrl['path'] . ( ! isset($parsedUrl['query']) ? '' : '?' . $parsedUrl['query']);
	}

	/**
	 * @param $url
	 * @return bool|string
	 */
	private function checkUrl($url)
	{
		$url = $this->prepareUrl($url);

		if(
			isset($this->register[parse_url($url)['path']])
			|| in_array(parse_url($url)['path'], $this->excludedUrls)
		){
			return false;
		}

		return $url;
	}

	/**
	 * Recursive function for searching and checking page status code
	 *
	 * @param string|Link $link
	 * @param bool|false $byRequest
	 */
	private function click($link, $byRequest = false)
	{
		$url = $this->checkUrl($byRequest ? $link : $link->getUri());

		if( ! $url)
		{
			return;
		}

		$crawler = $byRequest
			? $this->client->request('GET', $url)
			: $this->client->click($link);

		$response = $this->client->getResponse();

		$this->register[parse_url($url)['path']] = [
			'status_code' => $response->getStatusCode(),
			'is_successful' => $response->isSuccessful()
		];

		foreach($crawler->filter('a')->links() as $token)
		{
			$this->click($token);
		}
	}

	/**
	 * Prepare data for test
	 */
	protected function setUp()
	{
		$this->client = $this->createAdminAuthorizedClient();
		$container = $this->client->getContainer();
		$this->router = $container->get('router');
		$locales = explode('|', $container->getParameter('locales'));

		foreach(['fos_user_security_logout'] as $token)
		{
			foreach($locales as $locale)
			{
				$this->excludedUrls[] = $this->router->generate($token, ['_locale' => $locale]);
			}
		}
	}

	/**
	 * Test pages status
	 *
	 *
	 */
	public function testPages()
	{

		$this->click($this->router->generate('sonata_admin_dashboard'), true);

		foreach($this->register as $url => $data)
		{
			$this->assertTrue(
				in_array($data['status_code'], range(200, 400)),
				$data['status_code'] . "\t" . $url . "\n"
			);
        }
	}
}