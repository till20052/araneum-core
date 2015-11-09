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
        $menu = [
            [
                "text" => "Main Navigation",
                "heading" => "true",
                "translate" => "admin.sidebar.heading.HEADER"
            ],
            [
                "text" => "Dashboard",
                "sref" => "app.dashboard",
                "icon" => "icon-speedometer",
                "translate" => "admin.sidebar.nav.DASHBOARD"
            ],
            [
                "text" => "Users",
                "sref" => "app.table-ngtable",
                "icon" => "icon-users",
                "translate" => "admin.sidebar.nav.USERS"
            ],
            [
                "text" => "Site manager",
                "heading" => "true",
                "translate" => "admin.sidebar.heading.MANAGER"
            ],
            [
                "text" => "Cluster",
                "sref" => "app.table-standard",
                "icon" => "icon-grid",
                "translate" => "admin.sidebar.nav.manager.CLUSTER"
            ],
            [
                "text" => "Applications",
                "sref" => "app.application",
                "icon" => "icon-screen-tablet",
                "translate" => "admin.sidebar.nav.manager.APPLICATION"
            ],
            [
                "text" => "Connection",
                "sref" => "app.connections",
                "icon" => "icon-share-alt",
                "translate" => "admin.sidebar.nav.manager.CONNECTION"
            ],
            [
                "text" => "Component",
                "sref" => "app.components",
                "icon" => "icon-puzzle",
                "translate" => "admin.sidebar.nav.manager.COMPONENT"
            ],
            [
                "text" => "Locale",
                "sref" => "app.locales",
                "icon" => "icon-globe-alt",
                "translate" => "admin.sidebar.nav.manager.LOCALE"
            ],
            [
                "text" => "Received data",
                "heading" => "true",
                "translate" => "admin.sidebar.heading.RECEIVED"
            ],
            [
                "text" => "Customer",
                "sref" => "app.customers",
                "icon" => "icon-user-follow",
                "translate" => "admin.sidebar.nav.received.CUSTOMER"
            ],
            [
                "text" => "Email",
                "sref" => "app.emails",
                "icon" => "icon-layers",
                "translate" => "admin.sidebar.nav.received.EMAIL"
            ]
        ];

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

        return new JsonResponse(
            $translator->getMessages($translator->getLocale()),
            200
        );
    }
}