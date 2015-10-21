<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AdminApplicationController extends BaseCheckerController
{
    /**
     * Check Application Status State
     *
     * @Route("/admin/araneum/main/application/check_status_state/{id}", name="araneum_main_admin_application_check_status_state")
     *
     * @param $id
     * @return JsonResponse
     */
    public function checkStatusStateAction($id)
    {
        return $this->container
            ->get('araneum.main.application.checker')
            ->checkApplication($id);
    }

    /**
     * Check Application Statuses
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/araneum/main/application/batch", condition="request.request.get('data') matches '/checkStatus/'"
     *     ,name="araneum_main_admin_application_batchAction")
     */
    public function batchActionCheckStatus(Request $request)
    {
        $idx = parent::getIdxElements($request, 'araneum.main.admin.application');

        foreach ($idx as $id) {
            $this->container
                ->get('araneum.main.application.checker')
                ->checkApplication($id);
        }

        return new Response();
    }
}