<?php

namespace Araneum\Bundle\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Araneum\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;

class AdminUserController extends Controller
{
    public function activateUserAction()
    {
        // TODO: add you implementation here
    }

    public function recoverPasswordAction()
    {
        // TODO: add you implementation here
    }

    /**
     * Get user settings
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/get_settings/", name="araneum_user_get_settings")
     * @return JsonResponse
     *
     */
    public function getSettingsAction()
    {
        $user = $this->getUser();
        $response = new JsonResponse();

        if (is_null($user)) {
            $response->setStatusCode(403);
            $response->setContent('No authorized');
        } else {
            $response->setStatusCode(200);
            $response->setContent(json_encode($user->getSettings()));
        }

        return $response;
    }

    /**
     * Set user settings
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Post("/settings/set", defaults={"_format"="json"}, name="araneum_user_settings_set")
     * @param Request $request
     * @return JsonResponse $response
     */
    public function settingsSetAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        try {
            $user->setSettings($request->request->all());
            $em->persist($user);
            $em->flush();
            $response->setStatusCode(200);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setContent($e->getMessage());
        }

        return $response;
    }
}