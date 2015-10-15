<?php

namespace Araneum\Bundle\MainBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ClusterApiController extends FOSRestController
{
	/**
	 * Get configurations list of applications which cluster contains
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
	 * @Route(
	 *      "/cluster/applications_configs_list/{clusterId}",
	 *      name="araneum_main_api_cluster_applications_configs_list"
	 * )
	 * @Method("GET")
	 *
	 * @Rest\View()
	 *
	 * @param int $clusterId The cluster id
	 * @return array
	 */
	public function applicationsConfigsListAction($clusterId)
	{
		$list = $this->container
			->get('araneum.main.cluster.handler')
			->getApplicationsConfigsList($clusterId);

		if( ! $list)
			throw new NotFoundHttpException('Cluster not found');

		return $list;
	}
}