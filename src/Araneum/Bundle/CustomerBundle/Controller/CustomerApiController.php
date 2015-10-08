<?php

namespace Araneum\Bundle\CustomerBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CustomerApiController extends FOSRestController
{

    /**
     * Get Application config by apiKey
     *
     * @ApiDoc(
     *   resource = "Customer",
     *   section = "CustomerBundle",
     *   description = "Gets a Application config for a given key",
     *   output = "Araneum\Bundle\Customer\Entity\Application",
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
     * @Route("/customer/data/{apiKey}")
     *
     * @Method({"GET"})
     *
     * @param string $apiKey
     * @param        $customerData
     */
    public function getDataAction($apiKey, $customerData)
    {
    }
}