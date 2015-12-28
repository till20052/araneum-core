<?php

namespace Araneum\Bundle\UserBundle\Controller;

use Araneum\Bundle\UserBundle\Entity\User;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityController
 *
 * @package Araneum\Bundle\UserBundle\Controller
 */
class SecurityController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function loginAction()
    {
        /**
         * @var Request $request
         */
        $request = $this->container->get('request');

        if ($request->isXmlHttpRequest()) {

            /**
             * @var User $user
             */
            $user = $this->container
                ->get('security.token_storage')
                ->getToken()
                ->getUser();

            if ($user instanceof User) {
                return new JsonResponse(
                    [
                        'name' => $user->getFullName(),
                        'email' => $user->getEmail(),
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }

            $handler = $this->container->get('araneum.user.authentication_handler');

            return new JsonResponse($handler->login($request)['_csrf_token'], Response::HTTP_OK);
        }

        return parent::loginAction();
    }
}
