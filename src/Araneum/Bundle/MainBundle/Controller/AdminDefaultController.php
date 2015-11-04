<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
                "text" => "Site manager",
                "heading" => "true",
                "translate" => "sidebar.heading.MANAGER"
            ],
            [
                "text" => "Pages",
                "sref" => "app.code-editor",
                "icon" => "icon-screen-desktop",
                "translate" => "sidebar.nav.manager.PAGES"
            ],
            [
                "text" => "Main Navigation",
                "heading" => "true",
                "translate" => "sidebar.heading.HEADER"
            ],
            [
                "text" => "Users",
                "sref" => "app.table-ngtable",
                "icon" => "icon-users",
                "translate" => "sidebar.nav.USERS"
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
     * @Route("/manage/translate/{lang}.json", name="araneum_admin_translations")
     *
     * @param $lang
     * @return JsonResponse
     */
    public function translatesAction($lang = 'en')
    {
        $menu = [
            "topbar" => [
                "search" => [
                    "PLACEHOLDER" => "Type and hit enter..."
                ],
                "notification" => [
                    "MORE" => "More notifications"
                ]
            ],
            "offsidebar" => [
                "setting" => [
                    "SETTINGS" => "Settings",
                    "THEMES" => "Themes",
                    "layout" => [
                        "FIXED" => "Fixed",
                        "COLLAPSED" => "Collapsed"
                    ]
                ]
            ],
            "sidebar" => [
                "WELCOME" => "Welcome",
                "heading" => [
                    "HEADER" => "Main Navigation",
                    "MANAGER" => "Site manager"
                ],
                "nav" => [
                    "DASHBOARD" => "Dashboard",
                    "USERS" => "Users",
                    "manager" => [
                        "PAGES" => "Pages",
                        "LOCALE" => "Locales"
                    ]
                ]
            ]
        ];

        return new JsonResponse(
            $menu,
            200
        );
    }
}