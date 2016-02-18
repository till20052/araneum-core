<?php

namespace Araneum\Bundle\AgentBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Guzzle\Http\Exception\RequestException;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class SpotAdapterApiController
 *
 * @package Araneum\Bundle\AgentBundle\Controller
 */
class SpotAdapterApiController extends FOSRestController
{
    /**
     * Sends request to spot
     *
     * @ApiDoc(
     *   resource = "Request",
     *   section = "SpotAdapter",
     *   description = "Sends request to spot",
     *   filters = {
     *      {"name"="appKey", "dataType"="string" },
     *      {"name"="MODULE", "dataType"="string"},
     *      {"name"="COMMAND", "dataType"="string"},
     *      {"name"="requestData", "dataType"="string"},
     *      {"name"="guaranteeDelivery", "dataType"="boolean"},
     *   },
     *   output = "JSON",
     *   statusCodes = {
     *      201 = "Returned when request is successful",
     *      403 = "Returned when request is failed",
     *      404 = "Returned when Application not found"
     *   },
     *   tags={"Adapter"}
     * )
     * @Security("has_role('ROLE_API')")
     * @Rest\Post("/api/spot/request", defaults={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param  Request $request
     * @return mixed
     */
    public function spotRequestAction(Request $request)
    {
        $postParameters = $request->request->all();
        try {
            $form = $this->container
                ->get('araneum.agent.spot.adapter')
                ->sendRequestToSpot($postParameters);

            return View::create($form, 201);
        } catch (RequestException $e) {

            return View::create($e->getMessage(), 403);
        } catch (Exception $e) {

            return View::create($e->getMessage(), 404);
        }
    }
}
