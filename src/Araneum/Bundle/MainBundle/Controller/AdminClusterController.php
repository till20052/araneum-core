<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminClusterController extends Controller
{
    /**
     * Check Status
     *
     * @param $id
     * @return Response
     * @Route("/checkStatus/{id}", name="araneum_main_admin_cluster_checkStatus")
     */
    public function checkStatusAction($id)
    {
        var_dump($id);
        //TODO necessary to implement
        return new Response();
    }

    /**
     * Check Status
     *
     * @return Response
     * @Route("/admin/araneum/main/cluster/batch", condition="request.request.get('data') matches '/checkStatus/'" ,name="araneum_main_admin_cluster_batchAction")
     */
    public function batchActionCheckStatus()
    {
        die(var_dump(123123123123));
        var_dump($normalizedSelectedIds);

        return new Response();
    }
}