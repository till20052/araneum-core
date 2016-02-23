<?php
namespace Araneum\Bundle\AgentBundle\Controller;

use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\AgentBundle\Service\Actions\LeadActions;
use Araneum\Bundle\AgentBundle\Service\DataTable\LeadDataTableList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\All;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class AdminLeadController
 *
 * @package Araneum\Bundle\AgentBundle\Controller
 */
class AdminLeadController extends Controller
{
    /**
     * Get leads by id
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *      "/leads/leads/{id}",
     *      name="araneum_admin_agent_lead_get",
     *      requirements={"id" = "\d+"},
     *      defaults={"id" = null}
     * )
     * @Method("GET")
     * @param         int $id
     * @return        JsonResponse
     */
    public function getLeadJsonAction($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository('AraneumAgentBundle:Lead');

        $lead = $repository->findOneById($id);
        if (empty($lead)) {
            $lead = new Lead();
        };

        try {
            return new JsonResponse(
                $this
                    ->get('araneum.form_exporter.service')
                    ->get(
                        $this->get('araneum.agent.lead.form'),
                        $lead
                    ),
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                $exception->getMessage(),
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Delete leads one or many
     *
     * @ApiDoc(
     *  resource = "Lead",
     *  section = "AgentBundle",
     *  description = "Delete leads",
     *  requirements={
     *      {"name"="_format", "dataType"="json", "description"="Output format must be json"}
     *  },
     *  statusCodes = {
     *      400 = "Returned when validation failed",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Lead not found"
     *  },
     *  tags={"Agent"}
     * )
     *
     * @Route("/leads/leads/delete", defaults={"_format"="json"}, name="araneum_agent_admin_lead_delete")
     * @param                                  Request $request
     * @return                                 JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $idx = $request->request->get('data');
        $leadRepository = $this->getDoctrine()->getRepository('AraneumAgentBundle:Lead');

        if (is_array($idx) && count($idx) > 0) {
            $leadRepository->delete($idx);
        }

        return new JsonResponse('Success');
    }

    /**
     * Leads module initialization
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/leads/init.json", name="araneum_manage_lead_init")
     * @return                             JsonResponse
     */
    public function initAction()
    {
        $initializer = $this->get('araneum.admin.initializer.service');
        $filter = $this->get('araneum_agent.lead.filter.form');
        $code = JsonResponse::HTTP_OK;

        try {
            $initializer->setFilters($filter);
            $initializer->setGrid(
                new LeadDataTableList($this->container),
                $this->generateUrl('araneum_manage_leads_grid')
            );
            $initializer->setActions(new LeadActions());
        } catch (\Exception $exception) {
            $code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            $initializer->setError($exception);
        }

        return new JsonResponse($initializer->get(), $code);
    }

    /**
     * Server/client datatable communication
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/leads/datatable.json", name="araneum_manage_leads_grid")
     * @return                                  JsonResponse
     */
    public function datatableAction()
    {
        return $this
            ->get('araneum_datatable.factory')
            ->create(new LeadDataTableList($this->container))
            ->execute();
    }
}
