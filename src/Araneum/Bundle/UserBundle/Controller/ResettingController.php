<?php

namespace Araneum\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResettingController extends BaseController
{
    const SESSION_EMAIL = 'fos_user_send_resetting_email/email';

    /**
     * @var Translator
     */
    private $translator;

    private function t($id, $parameters = [])
    {
        if( ! $this->translator instanceof Translator){
            $this->translator = $this->container->get('translator.default');
        }

        return $this->translator->trans($id, $parameters, 'FOSUserBundle');
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction()
    {
        try {

            $username = $this->container->get('request')->request->get('username');

            /** @var $user UserInterface */
            $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

            if(empty($user)){
                throw new \Exception($this->t('resetting.request.invalid_username', ['username' => $username]));
            }

            if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
                throw new \Exception($this->t('resetting.password_already_requested'));
            }

            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
            $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->container->get('fos_user.user_manager')->updateUser($user);

            return new JsonResponse(['success' => true], Response::HTTP_OK);

        } catch (\Exception $exception) {

            return new JsonResponse(
                [
                    'success' => false,
                    'error' => $exception->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );

        }
    }

    /**
     * Reset user password
     */
    public function resetAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');
        $process = $formHandler->process($user);

        if ($process) {
            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $response = new RedirectResponse($this->getRedirectionUrl($user));
            $this->authenticateUser($user, $response);

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:reset.html.' . $this->getEngine(), array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }
}