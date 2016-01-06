<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class AdminDefaultController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class AdminDefaultController extends Controller
{
    /**
     * Araneum home page action
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/menu.json", name="araneum_admin_main_menu")
     * @return                     JsonResponse
     */
    public function menuAction()
    {
        $menu = $this->container
            ->get('araneum.main.menu.generator')
            ->leftMenuGenerate();

        return new JsonResponse(
            $menu,
            200
        );
    }

    /**
     * Araneum home page action
     *
     * @ApiDoc(
     *   resource = "Admin",
     *   section = "MainBundle",
     *   description = "Get Translates list",
     *   statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Translate list not found"
     *   },
     *   requirements = {
     *      {
     *          "name" = "_format",
     *          "dataType" = "json",
     *          "description" = "Output format must be json"
     *      }
     *   },
     *   tags={"AdminApi"}
     * )
     *
     * @Rest\Get("/manage/translates.json",         name="araneum_admin_translations")
     * @Rest\Get("%locale%/manage/translates.json", name="araneum_admin_translation_default_locale")
     * @return JsonResponse
     */
    public function getTranslatesAction()
    {
        $translator = $this->get('translator');

        $messages = $translator->getMessages($translator->getLocale());

        return new JsonResponse(
            $messages,
            200
        );
    }
}
