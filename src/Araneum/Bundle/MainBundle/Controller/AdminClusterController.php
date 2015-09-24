<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminClusterController extends Controller
{
    /**
     * Check Status
     *
     * @param $id
     * @return Response
     *
     * @Route("/checkStatus/{id}", name="araneum_main_admin_cluster_checkStatus")
     */
    public function checkStatusAction($id)
    {

        //TODO necessary to implement
        return new Response();
    }
}