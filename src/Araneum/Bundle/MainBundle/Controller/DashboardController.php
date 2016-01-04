<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Araneum\Bundle\MainBundle\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route(
     *     "/manage/dashboard/data-source.json",
     *     name="araneum_admin_dashboard_getDataSource"
     * )
     *
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
                'daylyApplications' => $service->prepareResulForDaylyApplications(),
                'daylyAverageStatuses' => $service->prepareResultForDaylyAverageStatuses(),
                'clusterLoadAverage' => $service->prepareResultForClusterAverage(),
                'clusterUpTime' => $service->prepareResultForClusterUpTime(),
                'summary' => $service->getSummary(),
                'registeredCustomers' => $service->getRegisteredCustomersFromApplications(),
                'receivedEmails' => $service->getReceivedEmailsFromApplications(),
            ],
            'charts' => [
                'leads' => $service->getRegisteredLeadsFromAppsInLast24H(),
                'errors' => $service->getReceivedErrorsFromAppsInLast24H(),
            ],
        ];

        return new JsonResponse($result, Response::HTTP_OK);
    }
}
