<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Araneum\Bundle\MainBundle\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DashboardController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class DashboardController extends Controller
{
    /**
     * Get General Data for Dashboard
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/manage/dashboard/data-source.json",
     *     name="araneum_admin_dashboard_getDataSource"
     * )
     * @return JsonResponse
     */
    public function getDataSourceAction()
    {
        /**
         * @var StatisticsService $service
         */
        $statisticService = $this->get('araneum.main.statistics.service');

        $result = [
            'statistics' => [
                'applicationsState' => $statisticService->getApplicationsStatistics(),
                'daylyApplications' => $statisticService->prepareResulForDaylyApplications(),
                'daylyAverageStatuses' => $statisticService->prepareResultForDaylyAverageStatuses(),
                'clusterLoadAverage' => $statisticService->prepareResultForClusterAverage(),
                'clusterUpTime' => $statisticService->prepareResultForClusterUpTime(),
                'summary' => $statisticService->getSummary(),
                'registeredCustomers' => $statisticService->getRegisteredCustomersFromApplications(),
                'receivedEmails' => $statisticService->getReceivedEmailsFromApplications(),
            ],
            'charts' => [
                'leads' => $statisticService->getLeads(),
            ],
        ];

        return new JsonResponse($result);
    }
}
