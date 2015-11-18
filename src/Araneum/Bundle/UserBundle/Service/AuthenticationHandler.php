<?php

namespace Araneum\Bundle\UserBundle\Service;

use Araneum\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfTokenManagerAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @var CsrfTokenManagerAdapter
	 */
	private $tokenManager;

	/**
	 * AuthenticationHandler constructor.
	 *
	 * @param Router $router
	 * @param Session $session
	 * @param CsrfTokenManagerAdapter $tokenManager
	 */
	public function __construct(Router $router, Session $session, CsrfTokenManagerAdapter $tokenManager)
	{
		$this->router = $router;
		$this->session = $session;
		$this->tokenManager = $tokenManager;
	}

	/**
	 * Login
	 *
	 * @param Request $request
	 * @param \Exception $error
	 * @return array
	 */
	public function login(Request $request, \Exception $error = null)
	{
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} elseif (null !== $this->session && $this->session->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $this->session->get(SecurityContext::AUTHENTICATION_ERROR);
			$this->session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}

		if ($error instanceof \Exception) {
			$error = $error->getMessage();
		}

		return [
			'_csrf_token' => $this->tokenManager->generateCsrfToken('authenticate'),
			'error' => $error
		];
	}

	/**
	 * This is called when an interactive authentication attempt succeeds. This
	 * is called by authentication listeners inheriting from
	 * AbstractAuthenticationListener.
	 *
	 * @param Request $request
	 * @param TokenInterface $token
	 *
	 * @return Response never null
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{
		if ($request->isXmlHttpRequest()) {
			/** @var User $user */
			$user = $token->getUser();

			return new JsonResponse(
				[
					'name' => $user->getFullName(),
					'email' => $user->getEmail()
				],
				Response::HTTP_OK
			);
		}

		if ($this->session->get('_security.main.target_path')) {
			$url = $this->session->get('_security.main.target_path');
		} else {
			$url = $this->router->generate('sonata_admin_dashboard');
		}

		return new RedirectResponse($url);
	}

	/**
	 * This is called when an interactive authentication attempt fails. This is
	 * called by authentication listeners inheriting from
	 * AbstractAuthenticationListener.
	 *
	 * @param Request $request
	 * @param AuthenticationException $exception
	 *
	 * @return Response The response to return, never null
	 */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		if ($request->isXmlHttpRequest()) {
			return new JsonResponse(
				$this->login($request, $exception),
				Response::HTTP_BAD_REQUEST
			);
		}

		$request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $exception);

		return new RedirectResponse($this->router->generate('fos_user_security_login'));
	}
}