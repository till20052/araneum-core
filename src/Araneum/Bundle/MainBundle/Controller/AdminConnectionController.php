<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Araneum\Base\Controller\AdminBaseController;

/**
 * Class AdminConnectionController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class AdminConnectionController extends AdminBaseController
{

    /**
     * Test connection
     *
     * @param int $id
     * @return Response
     * @Route("/testConnection/{id}", name="araneum_main_admin_connection_testConnection")
     */
    public function testConnectionAction($id)
    {
        $connection = $this->getDoctrine()->getRepository('AraneumMainBundle:Connection')->find($id);

        if ($connection->getType() == Connection::CONN_NGINX) {
            $this->container
                ->get('araneum.main.application.checker')
                ->checkConnection($id);
        }

        $request = $this->get('request');
        $referer = $request->headers->get('referer');

        if (is_null($referer)) {
            $referer = $this->get('router')->generate('admin_araneum_main_connection_list');
        }

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
    public function batchCheckStatusAction(Request $request)
    {
        $idx = parent::getIdxElements(json_decode($request->request->get('data')), 'araneum.main.admin.connection');
        $repository = $this->getDoctrine()->getRepository('AraneumMainBundle:Connection');
        $connections = $repository->getActiveHostConnections($idx);

        foreach ($connections as $connection) {
            $this->container
                ->get('araneum.main.application.checker')
                ->checkConnection($connection->getId());
        }

        return new RedirectResponse(
            $this->admin->generateUrl(
                'list',
                ['filter' => $this->admin->getFilterParameters()]
            )
        );
    }
}
