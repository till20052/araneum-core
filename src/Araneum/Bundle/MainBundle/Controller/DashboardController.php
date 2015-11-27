<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Araneum\Bundle\MainBundle\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    /**
     * Get General Data for Dashboard
     *
     * @Route(
     *     "/manage/dashboard/data-source.json",
     *     name="araneum_admin_dashboard_getDataSource"
     * )
     * @return JsonResponse
     */
    public function getDataSourceAction()
    {
        /** @var StatisticsService $service */
        $statisticService = $this->get('araneum.main.statistics.service');

        $result = [
            'statistics' => [
                'applicationsState' => $statisticService->getApplicationsStatistics(),
                'daylyApplications' => $statisticService->prepareResulForDaylyApplications(),
                'daylyAverageStatuses' => $statisticService->prepareResultForDaylyAverageStatuses(),
                'clusterLoadAverage' => $statisticService->prepareResultForClusterAverage(),
                'clusterUpTime' => $statisticService->prepareResultForClusterUpTime()
            ]
        ];

        return new JsonResponse($result, Response::HTTP_OK);
    }
}