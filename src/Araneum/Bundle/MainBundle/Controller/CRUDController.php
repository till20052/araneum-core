<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;

class CRUDController extends Controller
{
    public function testConnectionAction()
    {

        //TODO necessary to implement
        return new Response();
    }

    public function checkStatusAction($id){

        //TODO necessary to implement
        return new Response();
    }
}