<?php

namespace Araneum\Bundle\MainBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;

class ClusterApiController extends FOSRestController
{
	/**
	 * Get Application config by apiKey
	 *
	 * @ApiDoc(
	 *   resource = "Cluster",
	 *   section = "MainBundle",
	 *   description = "Gets a Application config for a given key",
	 *   output = "Araneum\Bundle\MainBundle\Entity\Application",
	 *   statusCodes = {
	 *      200 = "Returned when successful",
	 *      403 = "Returned when authorization is failed",
	 *      404 = "Returned when Application not found"
	 *   },
	 *   requirements = {
	 *      {
	 *          "name" = "_format",
	 *          "dataType" = "json",
	 *          "description" = "Output format must be json"
	 *      }
	 *   },
	 *   parameters={
	 *      {"name"="apiKey", "dataType"="string", "required"=true, "description"="apiKey"}
	 *   },
	 *   tags={"ApplicationApi"}
	 * )
	 *
	 * @Route("/application/config/{apiKey}", name="araneum_main_api_application")
	 * @Method({"GET"})
	 *
	 * @Rest\View(templateVar="application")
	 *
	 * @param string $apiKey The application apiKey
	 * @return array
	 */
	public function applicationsConfigsList()
	{

	}
}