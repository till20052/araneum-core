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
     *     "/manage/dashboard/data_source.json",
     *     name="araneum_admin_dashboard_getDataSource"
     * )
     * @return JsonResponse
     */
    public function getDataSourceAction()
    {
        /** @var StatisticsService $service */
        $service = $this->get('araneum.main.statistics.service');
        $statusesDayly = $service->getApplicationsStatusesDayly();
        $statusesDaylyAverage = $service->getAverageApplicationStatusesDayly();

        $result = [
            'statistics' => [
                'applicationsState' => $service->getApplicationsStatistics(),
                'daylyApplications' => [
                    'applications' => $service->getResultByColumnName($statusesDayly, 'name'),
                    'errors' => $service->getResultByColumnName($statusesDayly, 'errors'),
                    'problems' => $service->getResultByColumnName($statusesDayly, 'problems'),
                    'success' => $service->getResultByColumnName($statusesDayly, 'success'),
                    'disabled' => $service->getResultByColumnName($statusesDayly, 'disabled')
                ],
                'daylyAverageStatuses' => [
                    'errors' => $service->getStatusesByPeriod($statusesDaylyAverage, 'errors'),
                    'problems' => $service->getStatusesByPeriod($statusesDaylyAverage, 'problems'),
                    'success' => $service->getStatusesByPeriod($statusesDaylyAverage, 'success'),
                    'disabled' => $service->getStatusesByPeriod($statusesDaylyAverage, 'disabled')
                ]
            ]
        ];

        return new JsonResponse($result, Response::HTTP_OK);
    }
}