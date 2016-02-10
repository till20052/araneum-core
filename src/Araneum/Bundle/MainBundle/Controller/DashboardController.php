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
        $service = $this->get('araneum.main.statistics.service');

        $result = [
            'statistics' => [
                'applicationsState' => $service->getApplicationsStatistics(),
                'daylyApplications' => $service->prepareResulForDailyApplications(),
                'daylyAverageStatuses' => $service->prepareResultForDailyAverageStatuses(),
                'clusterLoadAverage' => $service->getResultForClusterAverage(),
                'clusterUpTime' => $service->getResultForClusterUpTime(),
                'summary' => $service->getSummary(),
                'registeredCustomers' => $service->getRegisteredCustomersFromApplications(),
                'receivedEmails' => $service->getReceivedEmailsFromApplications(),
            ],
            'charts' => [
                'leads' => $service->getRegisteredLeadsFromAppsInLast24H(),
                'errors' => $service->getReceivedErrorsFromAppsInLast24H(),
                'runners' => $service->getResultsForRunnersUpTime(),
            ],
        ];

        return new JsonResponse($result);
    }
}
