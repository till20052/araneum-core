<?php
namespace Araneum\Bundle\MainBundle\Controller;

use Araneum\Bundle\MainBundle\Service\Actions\ApplicationActions;
use Araneum\Bundle\MainBundle\Service\Actions\LocaleActions;
use Araneum\Bundle\MainBundle\Service\DataTable\ApplicationDataTableList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminApplicationController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class AdminApplicationController extends Controller
{
    /**
     * Applications module initialization
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/applications/init.json", name="araneum_manage_applications_init")
     * @return JsonResponse
     */
    public function initAction()
    {
        $initializer = $this->get('araneum.admin.initializer.service');
        $filter = $this->get('araneum_main.application.filter.form');
        $code = JsonResponse::HTTP_OK;

        try {
            $initializer->setFilters($filter);
            $initializer->setGrid(
                new ApplicationDataTableList($this->container),
                $this->generateUrl('araneum_manage_applications_grid')
            );
            $initializer->setActions(new ApplicationActions());
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
     * @Route("/manage/applications/datatable.json", name="araneum_manage_applications_grid")
     * @return JsonResponse
     */
    public function datatableAction()
    {
        return $this
            ->get('araneum_datatable.factory')
            ->create(new ApplicationDataTableList($this->container))
            ->execute();
    }
}
