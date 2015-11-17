<?php

namespace Araneum\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController
{
	/**
	 * @inheritdoc
	 *
	 * @return JsonResponse
	 */
	public function loginAction()
	{
		/** @var Request $request */
		$request = $this->container->get('request');

		if($request->isXmlHttpRequest()){
			$response = $this->container
				->get('araneum.user.authentication_handler')
				->login($request);

			return new JsonResponse($response['_csrf_token'], Response::HTTP_OK);
		}

		return parent::loginAction();
	}
}