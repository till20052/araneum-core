<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;

class CRUDController extends Controller
{
    public function testConnectionAction()
    {
        return new Response();
    }
}