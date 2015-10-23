<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Admin;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Araneum\Base\Controller\AdminBaseController;

class AdminClusterController extends AdminBaseController
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

        $request = $this->get('request');
        $referer = $request->headers->get('referer');

        if (is_null($referer)) {
            $referer = $this->get('router')->generate('admin_araneum_main_application_list');
        }

        return new RedirectResponse($referer);
    }

    /**
     * Check Statuses
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/araneum/main/cluster/batch", condition="request.request.get('data') matches '/checkStatus/'"
     *     ,name="araneum_main_admin_cluster_batchAction")
     */
    public function batchCheckStatusAction(Request $request)
    {
        $idx = parent::getIdxElements(json_decode($request->request->get('data')), 'araneum.main.admin.cluster');

        foreach ($idx as $id) {
            $this->container
                ->get('araneum.main.application.checker')
                ->checkCluster($id);
        }

        return new RedirectResponse(
            $this->admin->generateUrl(
                'list',
                ['filter' => $this->admin->getFilterParameters()]
            )
        );
    }
}