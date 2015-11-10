<?php

namespace Araneum\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecurityController
 * @package Araneum\Bundle\UserBundle\Controller
 */
class SecurityController extends Controller
{
    /**
     * @Route("/get_token", name="araneum_admin_get_token")
     *
     * @return string
     */
    public function getTokenAction()
    {
        return new JsonResponse(
            $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate'),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/login.html")
     *
     * @return Response
     */
    public function loginViewAction()
    {
        return $this->render('admin.login.html.twig',
            [
                '_csrf_token' => $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate')
            ]
        );
    }
}