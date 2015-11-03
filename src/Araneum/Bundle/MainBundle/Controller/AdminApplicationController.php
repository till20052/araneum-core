<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Araneum\Base\Controller\AdminBaseController;

class AdminApplicationController extends AdminBaseController
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
        $this->container
            ->get('araneum.main.application.checker')
            ->checkApplication($id);

        $request = $this->get('request');
        $referer = $request->headers->get('referer');

        if (is_null($referer)) {
            $referer = $this->get('router')->generate('admin_araneum_main_application_list');
        }

        return new RedirectResponse($referer);
    }

    /**
     * Check Application Statuses
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/araneum/main/application/batch", condition="request.request.get('data') matches '/checkStatus/'"
     *     ,name="araneum_main_admin_application_batchAction")
     */
    public function batchCheckStatusAction(Request $request)
    {
        $idx = $this->getIdxElements(json_decode($request->request->get('data')), 'araneum.main.admin.application');

        foreach ($idx as $id) {
            $this->container
                ->get('araneum.main.application.checker')
                ->checkApplication($id);
        }

        return new RedirectResponse(
            $this->admin->generateUrl(
                'list',
                ['filter' => $this->admin->getFilterParameters()]
            )
        );
    }
}