<?php

namespace Araneum\Bundle\AgentBundle\Controller;

use Araneum\Base\Exception\InvalidFormException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;

/**
 * Class ErrorApiController
 *
 * @package Araneum\Bundle\AgentBundle\Controller
 */
class ErrorApiController extends FOSRestController
{
    /**
     * Insert customer by appKey
     *
     * @Security("has_role='ROLE_API'")
     *
     * @ApiDoc(
     *   resource = "Error",
     *   section = "AgentBundle",
     *   description = "Get error from Application",
     *   output = "Code",
     *   statusCodes = {
     *      201 = "Returned when successful created",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Application not found"
     *   },
     *   input="Araneum\Bundle\AgentBundle\Form\Type\ErrorType",
     *   tags={"Agent"}
     * )
     *
     * @Rest\Post("/api/errors/insert/{appKey}", defaults={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param string  $appKey
     * @param Request $request
     * @return mixed
     */
    public function setErrorAction($appKey, Request $request)
    {
        $postParameters = $request->request->all();

        try {
            $form = $this->container
                ->get('araneum.agent.error.api_handler')
                ->post($appKey, $postParameters);

            return View::create($form, 201);
        } catch (InvalidFormException $e) {

            return View::create($e->getForm(), 400);
        }
    }
}
