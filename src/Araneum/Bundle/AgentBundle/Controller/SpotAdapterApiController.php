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
use Symfony\Component\Debug\Exception\ContextErrorException;

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
     *   statusCodes = {
     *      201 = "Returned when request is successful",
     *      403 = "Returned when request is failed",
     *      404 = "Returned when Application not found"
     *   },
     *   parameters={
     *      {"name"="guaranteeDelivery", "dataType"="boolean", "required"=false, "description"="Set if want to get data as rabbitmq service"},
     *   },
     *   tags={"Adapter"}
     * )
     * @Security("has_role('ROLE_API')")
     * @Rest\Post("/api/spot/request/{appKey}", defaults={"_format"="json"}, name="araneum_agent_spot_request")
     * @Rest\View(statusCode=201)
     *
     * @param  string  $appKey
     * @param  Request $request
     * @return mixed
     */
    public function spotRequestAction($appKey, Request $request)
    {
        $postParameters = $request->request->all();
        try {
            $form = $this->container
                ->get('araneum.agent.spot.adapter')
                ->sendRequestToSpot($appKey, $postParameters);

            return View::create($form, 201);
        } catch (RequestException $e) {

            return View::create($e->getMessage(), 403);
        } catch (ContextErrorException $e) {

            return View::create('Request data is not valid. Please, use JSON format', 403);
        } catch (Exception $e) {

            return View::create($e->getMessage(), 404);
        }
    }
}
