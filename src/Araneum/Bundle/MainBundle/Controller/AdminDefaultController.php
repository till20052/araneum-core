<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;


class AdminDefaultController extends Controller
{
    /**
     * Araneum home page action
     *
     * @Route("/manage/menu.json", name="araneum_admin_main_menu")
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
     * @Rest\Get("/manage/translates.json", name="araneum_admin_translations")
     *
     * @return JsonResponse
     */
    public function getTranslatesAction()
    {
        $translator = $this->get('translator');

        $messages = $translator->getMessages($translator->getLocale());

        foreach($messages['admin'] as $key => $value){
            $tokens = explode('.', $key);
            $messages[$tokens[0]][implode('.', array_slice($tokens, 1))] = $value;
        }

        return new JsonResponse(
            $messages,
            200
        );
    }
}