<?php

namespace Araneum\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ResettingController extends BaseController
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * Translate message by message id
     *
     * @param $id
     * @param array $parameters
     * @return string
     */
    private function trans($id, $parameters = [])
    {
        if (!$this->translator instanceof Translator) {
            $this->translator = $this->container->get('translator.default');
        }

        return $this->translator->trans($id, $parameters, 'FOSUserBundle');
    }

    /**
     * Convert children of FormView to Array
     *
     * @param FormView|array $children
     * @param array $fields
     * @return array
     */
    private function extract($children, array $fields = ['name', 'full_name', 'label', 'value'])
    {
        $list = [];

        if ($children instanceof FormView) {
            $children = $children->children;
        }

        foreach ($children as $name => $child) {
            if (!(count($child->children) > 0)) {
                $item = [];

                foreach ($fields as $field) {
                    if (!isset($child->vars[$field])) {
                        continue;
                    }

                    $item[$field] = $child->vars[$field];
                }

                $list[] = $item;
            } else {
                $list = $list + $this->extract($child->children, $fields);
            }
        }

        return $list;
    }

    /**
     * Request reset user password: submit form and send email
     *
     * @return JsonResponse
     *
     * @throws \Exception in case if User not found
     * @throws \Exception in case if access link of reset password was not expired
     */
    public function sendEmailAction()
    {
        try {

            $username = $this->container->get('request')->request->get('username');

            /** @var $user UserInterface */
            $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

            if (empty($user)) {
                throw new \Exception($this->trans('resetting.request.invalid_username', ['username' => $username]));
            }

            if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
                throw new \Exception($this->trans('resetting.password_already_requested'));
            }

            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->container->get('fos_user.user_manager')->updateUser($user);

            return new JsonResponse(
                [
                    'success' => true,
                    'email' => $this->getObfuscatedEmail($user)
                ],
                Response::HTTP_OK
            );

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
	 *
     * @param $token
     * @return JsonResponse
     *
     * @throws AuthenticationException in case if process of reset password was finished successfully
     * @throws BadRequestHttpException in case if user confirmation token does not exist
     * @throws NotAcceptableHttpException in case if request of reset password has expired
     */
    public function resetAction($token)
    {
        $response = new JsonResponse([], Response::HTTP_OK);

        try {

            /** @var Request $request */
			$request = $this->container->get('request');

			if($request->isMethod('GET')){
				return new RedirectResponse(
					$this->container
						->get('router')
						->generate(
							'manage_all',
							['path' => trim($request->getRequestUri(), '/')]
						)
				);
			}

            $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

            if (empty($user)) {
                throw new BadRequestHttpException(
                    sprintf('The user with "confirmation token" does not exist for value "%s"', $token)
                );
            }

            if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
                throw new NotAcceptableHttpException('The password reset request has expired');
            }

            $form = $this->container->get('fos_user.resetting.form');
            $formHandler = $this->container->get('fos_user.resetting.form.handler');

            if ($formHandler->process($user)) {
                throw new AuthenticationException();
            }

			$errors = $this->container
				->get('araneum.base.form.handler')
				->getErrorMessages($form);

			if(
				$request->request->count() > 0
				&& count($errors) > 0
			){
				throw new BadRequestHttpException(implode("\n", $errors));
			}

            return $response->setData($this->extract($form->createView()));

        } catch (AuthenticationException $exception) {

			return $response->setStatusCode(Response::HTTP_ACCEPTED);

		} catch (HttpException $exception) {

			return $response->setData(['error' => $exception->getMessage()])
				->setStatusCode($exception->getStatusCode());

        } catch (\Exception $exception) {

            return $response->setData(['error' => $exception->getMessage()])
                ->setStatusCode(Response::HTTP_BAD_REQUEST);

        }
    }
}