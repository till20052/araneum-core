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
                "text" => "Main Navigation",
                "heading" => "true",
                "translate" => "sidebar.heading.HEADER"
            ],
            [
                "text" => "Dashboard",
                "sref" => "app.dashboard",
                "icon" => "icon-speedometer",
                "translate" => "sidebar.nav.DASHBOARD"
            ],
            [
                "text" => "Users",
                "sref" => "app.table-ngtable",
                "icon" => "icon-users",
                "translate" => "sidebar.nav.USERS"
            ],
            [
                "text" => "Site manager",
                "heading" => "true",
                "translate" => "sidebar.heading.MANAGER"
            ],
            [
                "text" => "Cluster",
                "sref" => "app.table-standard",
                "icon" => "icon-grid",
                "translate" => "sidebar.nav.manager.CLUSTER"
            ],
            [
                "text" => "Applications",
                "sref" => "app.application",
                "icon" => "icon-screen-tablet",
                "translate" => "sidebar.nav.manager.APPLICATION"
            ],
            [
                "text" => "Connection",
                "sref" => "app.connections",
                "icon" => "icon-share-alt",
                "translate" => "sidebar.nav.manager.CONNECTION"
            ],
            [
                "text" => "Component",
                "sref" => "app.components",
                "icon" => "icon-puzzle",
                "translate" => "sidebar.nav.manager.COMPONENT"
            ],
            [
                "text" => "Locale",
                "sref" => "app.locales",
                "icon" => "icon-globe-alt",
                "translate" => "sidebar.nav.manager.LOCALE"
            ],
            [
                "text" => "Received data",
                "heading" => "true",
                "translate" => "sidebar.heading.RECEIVED"
            ],
            [
                "text" => "Customer",
                "sref" => "app.customers",
                "icon" => "icon-user-follow",
                "translate" => "sidebar.nav.received.CUSTOMER"
            ],
            [
                "text" => "Email",
                "sref" => "app.emails",
                "icon" => "icon-layers",
                "translate" => "sidebar.nav.received.EMAIL"
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