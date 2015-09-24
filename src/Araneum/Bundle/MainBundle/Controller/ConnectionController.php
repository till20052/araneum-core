<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;

class ConnectionController extends Controller
{
    /**
     * Action for Test Connection
     *
     * @return Response
     */
    public function testConnectionAction()
    {
        return new Response();
    }
}