<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * Admin panel action
     *
     * @Route("/manage/{path}", name="araneum_admin_index", requirements={"path"=".*"})
     */
    public function adminAction()
    {
        return $this->render(
            'admin.layout.html.twig',
            []
        );
    }
}
