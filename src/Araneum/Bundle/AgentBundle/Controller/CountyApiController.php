<?php

namespace Araneum\Bundle\AgentBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class CountyApiController
 *
 * @package Araneum\Bundle\AgentBundle\Controller
 */
class CountyApiController extends FOSRestController
{
    /**
     * Get countries
     *
     * @ApiDoc(
     *   resource = "SpotOption API",
     *   section = "MainBundle",
     *   description = "Gets list countries",
     *   output = "JSON",
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
     *   tags={"ApplicationApi"}
     * )
     *
     * @Rest\Get(
     *      "/api/get_countries/{appKey}",
     *      name="araneum_agent_api_get_countries",
     *      defaults={"_format"="json", "_locale"="en"}
     * )
     *
     * @Rest\View()
     * @param string $appKey
     * @return array
     */
    public function getCountriesAction($appKey)
    {
        return $this->container
            ->get('araneum.agent.spotoption.service')
            ->getCountries($appKey);
    }
}
