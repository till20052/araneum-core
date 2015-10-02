<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminApplicationController extends Controller
{
    /**
     * Check Application Status State
     *
     * @Route("/admin/araneum/main/application/check_status_state/{id}", name="araneum_main_admin_application_check_status_state")
     *
     * @param $id
     * @return JsonResponse
     */
    public function checkStatusStateAction($id)
    {
        return new JsonResponse(
            [
                'state' => 1
            ]
        );
    }
}