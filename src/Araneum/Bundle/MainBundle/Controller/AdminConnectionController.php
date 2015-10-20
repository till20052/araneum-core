<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminConnectionController extends Controller
{
    /**
     * Check Connection Status State
     *
     * @param $id
     * @Route("/check_status_state/{id}", name="araneum_main_admin_connection_check_status_state")
     *
     * @return JsonResponse
     */
    public function checkStatusStateAction($id)
    {
		$this->container->get('araneum.main.application.checker');

        return new JsonResponse(
	        [
	            'success' => 1
            ]
        );
    }
}