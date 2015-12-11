<?php

namespace Araneum\Bundle\AgentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Araneum\Bundle\AgentBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Index action of Default controller
     *
     * @Route("/")
     *
     * @return Response
     */
    public function indexAction()
    {
        return new Response();
    }
}
