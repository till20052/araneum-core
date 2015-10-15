<?php

namespace Araneum\Bundle\MainBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationApiController extends FOSRestController
{
    /**
     * Get Application config by appKey
     *
     * @ApiDoc(
     *   resource = "Application",
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
     *      {"name"="appKey", "dataType"="string", "required"=true, "description"="appKey"}
     *   },
     *   tags={"ApplicationApi"}
     * )
     *
     * @Route("/api/application/config/{appKey}", name="araneum_main_api_application")
     * @Method({"GET"})
     *
     * @Rest\View(templateVar="application")
     *
     * @param string $appKey The application appKey
     * @return array
     */
    public function getConfigAction($appKey)
    {
        return $this->container
            ->get('araneum.main.handler.application')
            ->get($appKey);
    }
}