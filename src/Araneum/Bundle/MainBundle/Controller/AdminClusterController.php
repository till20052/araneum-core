<?php
namespace Araneum\Bundle\MainBundle\Controller;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Service\Actions\ClusterActions;
use Araneum\Bundle\MainBundle\Service\DataTable\ClusterDataTableList;
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
 * Class AdminClusterController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class AdminClusterController extends Controller
{

    /**
     * Get cluster by id
     *
     * @ApiDoc(
     *  resource = "Cluster",
     *  section = "MainBundle",
     *  description = "Get cluster",
     *  requirements={
     *      {"name"="_format", "dataType"="json", "description"="Output format must be json"}
     *  },
     *  parameters={
     *      {"name"="id", "dataType"="int", "required"=true, "description"="Id"},
     *  },
     *  statusCodes = {
     *      200 = "Returned when reset customer password was successful",
     *      400 = "Returned when validation failed",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Application or Customer not found by defined condition"
     *  },
     *  tags={"Agent"}
     * )
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *      "/manage/clusters/cluster/{id}",
     *      name="araneum_admin_main_cluster_get",
     *      requirements={"id" = "\d+"},
     *      defaults={"id" = null}
     * )
     * @Method("GET")
     * @param         int $id
     * @return        JsonResponse
     */
    public function getClusterJsonAction($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository('AraneumMainBundle:Cluster');

        $cluster = $repository->findOneById($id);
        if (empty($cluster)) {
            $cluster = new Cluster();
        };

        try {
            return new JsonResponse(
                $this
                    ->get('araneum.form_exporter.service')
                    ->get(
                        $this->get('araneum.main.cluster.form'),
                        $cluster
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
     * Delete clusters one or many
     *
     * @ApiDoc(
     *  resource = "Cluster",
     *  section = "MainBundle",
     *  description = "Delete clusters",
     *  requirements={
     *      {"name"="_format", "dataType"="json", "description"="Output format must be json"}
     *  },
     *  parameters={
     *      {"name"="data", "dataType"="collection", "required"=true, "description"="array[id]"},
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
     * @Route("/manage/clusters/cluster/delete", defaults={"_format"="json"}, name="araneum_main_admin_cluster_delete")
     * @param                                  Request $request
     * @return                                 JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $idx = $request->request->get('data');
        $clusterRepository = $this->getDoctrine()->getRepository('AraneumMainBundle:Cluster');

        if (is_array($idx) && count($idx) > 0) {
            $clusterRepository->delete($idx);
        }

        return new JsonResponse('Success');
    }

    /**
     * Save cluster
     *
     * @ApiDoc(
     *  resource = "Cluster",
     *  section = "MainBundle",
     *  description = "Save cluster",
     *  requirements={
     *      {"name"="_format", "dataType"="json", "description"="Output format must be json"}
     *  },
     *  input = {
     *      "class"="Araneum\Bundle\MainBundle\Form\Type\ClusterType",
     *      "name"=""
     *  },
     *  statusCodes = {
     *      201 = "Returned when cluster was created",
     *      202 = "Returned when cluster was updated",
     *      400 = "Returned when validation failed",
     *      403 = "Returned when authorization is failed",
     *      500 = "Returned when internal error occurred"
     *  },
     *  tags={"Agent"}
     * )
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/manage/clusters/cluster/save",
     *     name="araneum_admin_main_cluster_post"
     * )
     * @Method("POST")
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function saveClusterPostAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AraneumMainBundle:Cluster');

        try {
            if (!empty($id)) {
                $cluster = $repository->findOneById($id);
                $code = JsonResponse::HTTP_ACCEPTED;
            } else {
                $cluster = new Cluster();
                $code = JsonResponse::HTTP_CREATED;
            }

            $form = $this->createForm($this->get('araneum.main.cluster.form'), $cluster);
            $form->submit($request->request->all());

            if ($form->isValid()) {
                $em->persist($cluster);
                $em->flush();

                return new JsonResponse(
                    [
                        'message' => 'Cluster has been saved',
                        'id' => $cluster->getId(),
                    ],
                    $code
                );
            } else {

                return new JsonResponse(
                    ['message' => (string) $form->getErrors(true, false)],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
        } catch (\Exception $exception) {

            return new JsonResponse(
                ['message' => $exception->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Enable clusters one or many
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/clusters/cluster/enable", name="araneum_main_admin_cluster_enable")
     * @Method("POST")
     * @param          Request $request
     * @return         Response
     */
    public function enableAction(Request $request)
    {
        return $this->updateClusterEnableDisableAction($request, true);
    }

    /**
     * Disable clusters one or many
     *
     * @param Request $request
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/clusters/cluster/disable",name="araneum_main_admin_cluster_disable")
     */
    public function disableAction(Request $request)
    {
        return $this->updateClusterEnableDisableAction($request, false);
    }

    /**
     * Clusters module initialization
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/clusters/init.json", name="araneum_manage_clusters_init")
     * @return                             JsonResponse
     */
    public function initAction()
    {
        $initializer = $this->get('araneum.admin.initializer.service');
        $filter = $this->get('araneum_main.cluster.filter.form');
        $code = JsonResponse::HTTP_OK;

        try {
            $initializer->setFilters($filter);
            $initializer->setGrid(
                new ClusterDataTableList($this->container),
                $this->generateUrl('araneum_manage_clusters_grid')
            );
            $initializer->setActions(new ClusterActions());
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
     * @Route("/manage/clusters/datatable.json", name="araneum_manage_clusters_grid")
     * @return JsonResponse
     */
    public function datatableAction()
    {
        return $this
            ->get('araneum_datatable.factory')
            ->create(new ClusterDataTableList($this->container))
            ->execute();
    }

    /**
     * Check cluster status
     *
     * @param Request $request
     * @return JsonResponse
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/clusters/cluster/status",name="araneum_main_admin_cluster_status")
     */
    public function checkClusterStatusAction(Request $request)
    {
        $data = (array) $request->get('data');
        $id = array_shift($data);

        $errors = $this->get('validator')->validate($id, new Regex('/^\d+$/'));
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors);
        }

        $this->get('araneum.main.application.checker')->checkCluster($id);

        return new JsonResponse('Success');
    }

    /**
     * Update cluster state
     *
     * @param  Request $request
     * @param  bool    $state
     * @return JsonResponse
     */
    private function updateClusterEnableDisableAction(Request $request, $state)
    {
        $idx = $request->request->get('data');

        $clusterRepository = $this->getDoctrine()->getRepository('AraneumMainBundle:Cluster');

        if (!is_array($idx)) {
            return new JsonResponse('Data must be an array');
        }

        $errors = $this->get('validator')->validate($idx, new All([new Regex('/^\d+$/')]));
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors);
        }

        $clusterRepository->updateEnabled($idx, $state);

        return new JsonResponse('Success');
    }
}
