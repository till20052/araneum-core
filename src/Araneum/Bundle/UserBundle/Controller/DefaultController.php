<?php

namespace Araneum\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Araneum\Bundle\UserBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Rendering login view for AngularJS
     *
     * @Route("/login.html")
     *
     * @return Response
     */
    public function loginViewAction()
    {
        return $this->render(
            '::admin.login.html.twig',
            [
                '_csrf_token' => $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate'),
            ]
        );
    }

    /**
     * Render reset sub view of recover
     */
    public function resetViewAction()
    {
    }
}
