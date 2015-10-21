<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Araneum\Base\Controller\AdminBaseController;

class AdminConnectionController extends AdminBaseController
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
        $this->container
            ->get('araneum.main.application.checker')
            ->checkConnection($id);

        $request = $this->get('request');
        $referer = $request->headers->get('referer');

        return new RedirectResponse($referer);
    }

    /**
     * Check Statuses
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/araneum/main/connection/batch", condition="request.request.get('data') matches '/checkStatus/'"
     *     ,name="araneum_main_admin_connection_batchAction")
     */
    public function batchActionCheckStatus(Request $request)
    {
        $idx = parent::getIdxElements(json_decode($request->request->get('data')), 'araneum.main.admin.connection');

        foreach ($idx as $id) {
            $this->container
                ->get('araneum.main.application.checker')
                ->checkConnection($id);
        }

        return new RedirectResponse(
            $this->admin->generateUrl(
                'list',
                ['filter' => $this->admin->getFilterParameters()]
            )
        );
    }
}