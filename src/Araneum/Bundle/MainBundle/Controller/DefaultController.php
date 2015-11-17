<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * Admin panel action
     *
     * @Route(
     *     "/manage/{path}",
     *     name="araneum_admin_index",
     *     requirements={"path"=".*"},
     *     defaults={"path"=""}
     * )
     *
     * @Route(
     *     "/manage/resetting/reset/{token}",
     *     requirements={"token"=".*"},
     *     name="araneum_admin_resetting_reset"
     * )
     *
     * @return Response
     */
    public function adminAction()
    {
        $session = new Session();

		$session->set('test', 'value');

        return $this->render('admin.layout.html.twig');
    }
}
