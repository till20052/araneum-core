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
     * @Route("/clusterGetApp/{id}", name="araneum_main_admin_cluster_get_app")
     */
    public function checkStatusAction($id)
    {
        $list = $this->container
            ->get('araneum.main.application.remote_manager')
            ->get($id);

        return new Response();
    }
}