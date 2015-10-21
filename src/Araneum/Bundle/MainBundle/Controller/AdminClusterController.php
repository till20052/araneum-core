<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Admin;
use Symfony\Component\Routing\Annotation\Route;

class AdminClusterController extends BaseCheckerController
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
        $this->container
            ->get('araneum.main.application.checker')
            ->checkCluster($id);

        return new Response();
    }

    /**
     * Check Statuses
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/araneum/main/cluster/batch", condition="request.request.get('data') matches '/checkStatus/'"
     *     ,name="araneum_main_admin_cluster_batchAction")
     */
    public function batchActionCheckStatus(Request $request)
    {
        $idx = parent::getIdxElements($request, 'araneum.main.admin.cluster');

        foreach ($idx as $id) {
            $this->container
                ->get('araneum.main.application.checker')
                ->checkCluster($id);
        }

        return new Response();
    }
}