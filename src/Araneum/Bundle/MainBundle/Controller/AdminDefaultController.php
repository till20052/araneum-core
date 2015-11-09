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
     * @Route("/manage/translate/{lang}.json", name="araneum_admin_translations")
     *
     * @param $lang
     * @return JsonResponse
     */
    public function translatesAction($lang = 'en')
    {
        $translates = [
            "dashboard" => [
                "WELCOME" => "Welcome to Site manager"
            ],
            "topbar" => [
                "messages" => [
                    "UNREAD" => "Unread messages",
                    "MORE" => "More messages"
                ],
                "search" => [
                    "PLACEHOLDER" => "Type and hit enter.."
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
                ],
                "chat" => [
                    "CONNECTIONS" => "Connections",
                    "SEARCH" => "Search contacts",
                    "ONLINE" => "ONLINE",
                    "OFFLINE" => "OFFLINE",
                    "LOAD_MORE" => "Load More",
                    "TASK_COMPLETION" => "Tasks completion",
                    "QUOTA" => "Upload Quota"
                ]
            ],
            "sidebar" => [
                "WELCOME" => "Welcome",
                "heading" => [
                    "HEADER" => "Main Navigation",
                    "MANAGER" => "Site manager",
                    "RECEIVED" => "Received data"
                ],
                "nav" => [
                    "DASHBOARD" => "Dashboard",
                    "USERS" => "Users",
                    "manager" => [
                        "CLUSTER" => "Clusters",
                        "APPLICATION" => "Applications",
                        "CONNECTION" => "Connections",
                        "COMPONENT" => "Components",
                        "LOCALE" => "Locales"
                    ],
                    "received" => [
                        "CUSTOMER" => "Customers",
                        "EMAIL" => "Emails"
                    ],
                    "map" => [
                        "MAP" => "Maps",
                        "GOOGLE" => "Google Map",
                        "VECTOR" => "Vector Map"
                    ],
                    "extra" => [
                        "EXTRA" => "Extras",
                        "MAILBOX" => "Mailbox",
                        "TIMELINE" => "Timeline",
                        "CALENDAR" => "Calendar",
                        "INVOICE" => "Invoice",
                        "SEARCH" => "Search",
                        "TODO" => "Todo List",
                        "PROFILE" => "Profile"
                    ],
                    "pages" => [
                        "PAGES" => "Pages",
                        "LOGIN" => "Login",
                        "REGISTER" => "Register",
                        "RECOVER" => "Recover password",
                        "PROFILE" => "Profile",
                        "LOCK" => "Lock",
                        "STARTER" => "Starter Template"
                    ]
                ]
            ]
        ];

        return new JsonResponse(
            $translates,
            200
        );
    }
}