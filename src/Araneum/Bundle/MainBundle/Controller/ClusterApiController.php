<?php

namespace Araneum\Bundle\MainBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ClusterApiController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class ClusterApiController extends FOSRestController
{
    /**
     * Get configurations list of applications which cluster from core
     *
     * @ApiDoc(
     *   resource = "Cluster",
     *   section = "MainBundle",
     *   description = "Get configurations list of applications which cluster contains",
     *   statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Cluster not found"
     *   },
     *   requirements = {
     *      {
     *          "name" = "_format",
     *          "dataType" = "json",
     *          "description" = "Output format must be json"
     *      }
     *   },
     *   parameters={
     *      {"name"="clusterId", "dataType"="int", "required"=true, "description"="The cluster id"}
     *   },
     *   tags={"ClusterApi"}
     * )
     *
     * @Rest\Get(
     *      "/cluster/applications_configs_list/{clusterId}",
     *      name="araneum_main_api_cluster_applications_configs_list",
     *      defaults={"_format"="json", "_locale"="en"}
     * )
     *
     * @Rest\View()
     *
     * @param  int $clusterId The cluster id
     * @return array
     */
    public function applicationsCoreConfigsListAction($clusterId)
    {
        $list = $this->container
            ->get('araneum.main.cluster.api_handler')
            ->getApplicationsConfigsList($clusterId);

        if ($list === false) {
            throw new NotFoundHttpException('Cluster not found');
        }

        return $list;
    }

    /**
     * Get configurations list of applications which cluster from cluster
     *
     * @ApiDoc(
     *   resource = "Cluster",
     *   section = "MainBundle",
     *   description = "Get configurations list of applications which cluster contains",
     *   statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Cluster not found"
     *   },
     *   requirements = {
     *      {
     *          "name" = "_format",
     *          "dataType" = "json",
     *          "description" = "Output format must be json"
     *      }
     *   },
     *   parameters={
     *      {"name"="clusterId", "dataType"="int", "required"=true, "description"="The cluster id"}
     *   },
     *   tags={"ClusterApi"}
     * )
     *
     * @Rest\Get(
     *      "/api/getAppConfig/{clusterId}",
     *      name="araneum_main_api_cluster_remote_application_config",
     *      defaults={"_format"="json", "_locale"="en"}
     * )
     *
     * @Rest\View()
     *
     * @param  int $clusterId The cluster id
     * @return array
     */
    public function remoteApplicationGetDataAction($clusterId)
    {
        $list = $this->container
            ->get('araneum.main.application.remote_manager')
            ->get($clusterId);

        return $list;
    }
}
