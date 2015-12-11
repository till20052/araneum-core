<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Admin panel action
     *
     * @return Response
     */
    public function adminAction()
    {
        return $this->render('admin.layout.html.twig');
    }
}
