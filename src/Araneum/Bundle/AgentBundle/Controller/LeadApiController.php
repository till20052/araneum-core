<?php

namespace Araneum\Bundle\AgentBundle\Controller;

use Araneum\Base\Exception\InvalidFormException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LeadApiController extends Controller
{
	/**
	 * Find list of leads by email and/or phone as optionality
	 *
	 * @ApiDoc(
	 *  resource = "Lead",
	 *  section = "AgentBundle",
	 *  description = "Find list of leads by email and/or phone as optionality",
	 *  filters = {
	 *      {"name"="email", "dataType"="string"},
	 *      {"name"="phone", "dataType"="string"}
	 *  },
	 *  statusCodes = {
	 *      200 = "Returned when successful",
	 *      403 = "Returned when authorization is failed",
	 *      404 = "Returned when searching have no results"
	 *  },
	 *  tags={"Agent"}
	 * )
	 *
	 * @Rest\Get("/api/lead/find", defaults={"_format"="json"})
	 * @Rest\QueryParam(name="filters", array=true)
	 * @Rest\View(statusCode=200)
	 *
	 * @param ParamFetcher $paramFetcher
	 * @return array
	 */
	public function findAction(ParamFetcher $paramFetcher)
	{
		try {
			$list = $this
				->get('araneum.agent.lead.api_handler')
				->find($paramFetcher->get('filters'));

			if (!(count($list) > 0)) {
				throw new \Exception('Search return empty response');
			}

			return $list;
		} catch (\Exception $exception) {
			return View::create(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
		}
	}

	/**
	 * Create lead
	 *
	 * @ApiDoc(
	 *  resource = "Lead",
	 *  section = "AgentBundle",
	 *  description = "Create lead",
	 *  input={
	 *      "class"="\Araneum\Bundle\AgentBundle\Form\Type\LeadType",
	 *      "name"=""
	 *  },
	 *  statusCodes = {
	 *      201 = "Returned when lead created successful",
	 *      400 = "Returned when validation failed",
	 *      403 = "Returned when authorization is failed",
	 *      404 = "Returned when searching have no results"
	 *  },
	 *  tags={"Agent"}
	 * )
	 *
	 * @Rest\Post("/api/lead/create", defaults={"_format"="json"})
	 * @Rest\View(statusCode=201)
	 *
	 * @param Request $request
	 * @return array
	 */
	public function createAction(Request $request)
	{
		try {
			$this->get('araneum.agent.lead.api_handler')
				->create($request->request->all());

			return ['success' => true];
		} catch (InvalidFormException $exception) {
			return View::create($exception->getForm(), 400);
		}
	}
}