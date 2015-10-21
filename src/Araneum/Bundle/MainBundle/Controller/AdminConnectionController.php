<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminConnectionController extends Controller
{

    /**
     * Test connection
     *
     * @param $id
     * @return Response
     * @Route("/testConnection/{id}", name="araneum_main_admin_connection_testConnection")
     */
    public function testConnectionAction($id)
    {
        return new Response();
    }
}