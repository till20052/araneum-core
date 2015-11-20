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
                'daylyApplications' =>[
                    'applications'=>$service->getApplications($statusesDayly),
                    'errors' =>$service->getErrors($statusesDayly),
                    'problems' => $service->getProblems($statusesDayly),
                    'success' =>$service->getSuccess($statusesDayly),
                    'disabled' =>$service->getDisabled($statusesDayly)
                ],
                'daylyAverageStatuses'=>[
                    'errors' =>[$service->getErrorsByHours($statusesDaylyAverage)],
                    'problems' =>[$service->getProblemsByHours($statusesDaylyAverage)],
                    'success' =>[$service->getSuccessByHours($statusesDaylyAverage)],
                    'disabled' =>[$service->getDisabledByHours($statusesDaylyAverage)]
                ]
            ]
        ];

        return new JsonResponse($result, Response::HTTP_OK);
    }
}