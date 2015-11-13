<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * Admin panel action
     *
     * @Route("/manage/{path}", requirements={"path"=".*"}, defaults={"path"=""}, name="araneum_admin_index")
     */
    public function adminAction()
    {
        return $this->render(
            'admin.layout.html.twig',
            []
        );
    }
}
