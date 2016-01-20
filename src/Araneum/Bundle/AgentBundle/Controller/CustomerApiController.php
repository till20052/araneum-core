<?php

namespace Araneum\Bundle\AgentBundle\Controller;

use Araneum\Base\Exception\InvalidFormException;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class CustomerApiController
 *
 * @package Araneum\Bundle\AgentBundle\Controller
 */
class CustomerApiController extends FOSRestController
{
    /**
     * Insert customer by appKey
     *
     * @ApiDoc(
     *   resource = "Customer",
     *   section = "AgentBundle",
     *   description = "Sets a Customer a given key",
     *   output = "Code",
     *   statusCodes = {
     *      201 = "Returned when successful created",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Application not found"
     *   },
     *   input="Araneum\Bundle\AgentBundle\Form\Type\CustomerType",
     *   tags={"Agent"}
     * )
     * @Security("has_role('ROLE_API')")
     * @Rest\Post("/api/customers/insert/{appKey}", defaults={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param  string  $appKey
     * @param  Request $request
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

    /**
     * Login Customer
     *
     * @ApiDoc(
     *   resource = "Customer",
     *   section = "AgentBundle",
     *   description = "Logs login customer",
     *   statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when validation is failed",
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
     *      {"name"="appKey", "dataType"="string", "required"=true, "description"="appKey"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="email"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="password"},
     *   },
     *   tags={"Agent"}
     * )
     *
     * @Rest\Post("/api/customers/login/{appKey}", defaults={"_format"="json"})
     *
     * @Rest\View(statusCode=200)
     * @param string       $appKey
     * @param ParamFetcher $request
     * @Rest\RequestParam(name="email", allowBlank=false, requirements=".{2,}")
     * @Rest\RequestParam(name="password", allowBlank=false, requirements=".{6,}")
     * @return mixed
     */
    public function loginAction($appKey, ParamFetcher $request)
    {
        try {
            $email = $request->get('email');
            $password = $request->get('password');
            $result = $this->container
                ->get('araneum.agent.customer.api_handler')
                ->login($email, $password, $appKey);

            if ($result === false) {
                return View::create(["errors" => "Wrong username or password"], Response::HTTP_BAD_REQUEST);
            }

            return View::create($result);
        } catch (BadRequestHttpException $e) {
            return View::create($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return View::create($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Reset Customer Password
     *
     * @ApiDoc(
     *  resource = "Customer",
     *  section = "AgentBundle",
     *  description = "Reset Customer Password",
     *  requirements={
     *      {"name"="_format", "dataType"="json", "description"="Output format must be json"}
     *  },
     *  parameters={
     *      {"name"="app_key", "dataType"="string", "required"=true, "description"="Searching Application by this
     *     parameter"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Searching Customer by this
     *     parameter"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Customer new password"},
     *      {"name"="customer_id", "dataType"="int", "required"=true, "description"="Customer spot id"}
     *  },
     *  statusCodes = {
     *      202 = "Returned when reset customer password was successful",
     *      400 = "Returned when validation failed",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Application or Customer not found by defined condition"
     *  },
     *  tags={"Agent"}
     * )
     *
     * @Rest\Post("/api/customers/reset_password", defaults={"_format"="json"})
     * @Rest\QueryParam(name="app_key",            allowBlank=false)
     * @Rest\RequestParam(name="email",            allowBlank=false)
     * @Rest\RequestParam(name="customer_id",      allowBlank=false)
     * @Rest\RequestParam(name="password",         allowBlank=false, requirements=".{6,}")
     * @Rest\View(statusCode=200)
     * @Security("has_role('ROLE_API')")
     *
     * @param  ParamFetcher $paramFetcher
     * @return array
     */
    public function resetPasswordAction(ParamFetcher $paramFetcher)
    {
        try {
            $status = $this->container
                ->get('araneum.agent.customer.api_handler')
                ->resetPassword(
                    $paramFetcher->get('app_key'),
                    $paramFetcher->get('email'),
                    $paramFetcher->get('customer_id'),
                    $paramFetcher->get('password')
                );

            return ['status' => $status];
        } catch (\Exception $exception) {
            return View::create(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
