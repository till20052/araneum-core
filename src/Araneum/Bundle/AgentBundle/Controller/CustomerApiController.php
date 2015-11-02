<?php

namespace Araneum\Bundle\AgentBundle\Controller;

use Araneum\Base\Exception\InvalidFormException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;
use Araneum\Bundle\AgentBundle\Entity\Customer;
use Araneum\Bundle\AgentBundle\Form\CustomerType;
use FOS\RestBundle\View\View;

class CustomerApiController extends FOSRestController
{
    /**
     * Get Application config by appKey
     *
     * @ApiDoc(
     *   resource = "Customer",
     *   section = "AgentBundle",
     *   description = "Gets a Application config for a given key",
     *   output = "Araneum\Bundle\Agent\Entity\Application",
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
     * @Rest\Post("/api/customer/insert/{appKey}", defaults={"_format"="json"})
     *
     * @Rest\View(statusCode=201)
     *
     * @param string $appKey
     * @param Request $request
     * @return mixed
     */
    public function setCustomerAction($appKey, Request $request)
    {
        $postParameters = $request->request->all();

        try {
            $form = $this->container
                ->get('araneum.agent.customer.api_handler')
                ->post($appKey, $postParameters);

            return View::create($form, 201);

        } catch (InvalidFormException $e) {
            return View::create($e->getForm(), 400);
        }
    }
}