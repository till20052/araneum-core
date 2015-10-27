<?php

namespace Araneum\Bundle\AgentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

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
