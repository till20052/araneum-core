<?php
namespace Araneum\Bundle\MainBundle\Controller;

use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Service\Actions\LocaleActions;
use Araneum\Bundle\MainBundle\Service\DataTable\LocaleDataTableList;
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
 * Class AdminLocaleController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class AdminLocaleController extends Controller
{
    /**
     * Get locale by id
     *
     * @ApiDoc(
     *  resource = "Locale",
     *  section = "MainBundle",
     *  description = "Get locale",
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
     *      "/manage/locales/locale/{id}",
     *      name="araneum_admin_main_locale_get",
     *      requirements={"id" = "\d+"},
     *      defaults={"id" = null}
     * )
     * @Method("GET")
     * @param         int $id
     * @return        JsonResponse
     */
    public function getLocaleJsonAction($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository('AraneumMainBundle:Locale');

        $locale = $repository->findOneById($id);
        if (empty($locale)) {
            $locale = new Locale();
        };

        try {
            return new JsonResponse(
                $this
                    ->get('araneum.form_exporter.service')
                    ->get(
                        $this->get('araneum.main.locale.form'),
                        $locale
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
     * Delete locales one or many
     *
     * @ApiDoc(
     *  resource = "Locale",
     *  section = "MainBundle",
     *  description = "Delete locales",
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
     * @Route("/manage/locales/locale/delete", defaults={"_format"="json"}, name="araneum_main_admin_locale_delete")
     * @param                                  Request $request
     * @return                                 JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $idx = $request->request->get('data');
        $localeRepository = $this->getDoctrine()->getRepository('AraneumMainBundle:Locale');

        if (is_array($idx) && count($idx) > 0) {
            $localeRepository->delete($idx);
        }

        return new JsonResponse('Success');
    }

    /**
     * Save locale
     *
     * @ApiDoc(
     *  resource = "Locale",
     *  section = "MainBundle",
     *  description = "Save locale",
     *  requirements={
     *      {"name"="_format", "dataType"="json", "description"="Output format must be json"}
     *  },
     *  input = {
     *      "class"="Araneum\Bundle\MainBundle\Form\Type\LocaleType",
     *      "name"=""
     *  },
     *  statusCodes = {
     *      201 = "Returned when locale was created",
     *      202 = "Returned when locale was updated",
     *      400 = "Returned when validation failed",
     *      403 = "Returned when authorization is failed",
     *      500 = "Returned when Application or Customer not found by defined condition"
     *  },
     *  tags={"Agent"}
     * )
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/manage/locales/locale/save",
     *     name="araneum_admin_main_locale_post"
     * )
     * @Method("POST")
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function saveLocalePostAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AraneumMainBundle:Locale');

        try {
            if (!empty($id)) {
                $locale = $repository->findOneById($id);
                $code = JsonResponse::HTTP_ACCEPTED;
            } else {
                $locale = new Locale();
                $code = JsonResponse::HTTP_CREATED;
            }

            $form = $this->createForm($this->get('araneum.main.locale.form'), $locale);
            $form->submit($request->request->all());

            if ($form->isValid()) {
                $em->persist($locale);
                $em->flush();

                return new JsonResponse(
                    [
                        'message' => 'Locale has been saved',
                        'id' => $locale->getId(),
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
     * Enable locales one or many
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/locales/locale/enable", name="araneum_main_admin_locale_enable")
     * @Method("POST")
     * @param          Request $request
     * @return         Response
     */
    public function enableAction(Request $request)
    {
        return $this->updateLocaleEnableDisableAction($request, true);
    }

    /**
     * Disable locales one or many
     *
     * @param                                                                            Request $request
     * @return                                                                           Response
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/locales/locale/disable",name="araneum_main_admin_locale_disable")
     */
    public function disableAction(Request $request)
    {
        return $this->updateLocaleEnableDisableAction($request, false);
    }

    /**
     * Locales module initialization
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/manage/locales/init.json", name="araneum_manage_locales_init")
     * @return                             JsonResponse
     */
    public function initAction()
    {
        $initializer = $this->get('araneum.admin.initializer.service');
        $filter = $this->get('araneum_main.locale.filter.form');
        $code = JsonResponse::HTTP_OK;

        try {
            $initializer->setFilters($filter);
            $initializer->setGrid(
                new LocaleDataTableList($this->container),
                $this->generateUrl('araneum_manage_locales_grid')
            );
            $initializer->setActions(new LocaleActions());
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
     * @Route("/manage/locales/datatable.json", name="araneum_manage_locales_grid")
     * @return                                  JsonResponse
     */
    public function datatableAction()
    {
        return $this
            ->get('araneum_datatable.factory')
            ->create(new LocaleDataTableList($this->container))
            ->execute();
    }

    /**
     * Update locale state
     *
     * @param  Request $request
     * @param  bool    $state
     * @return JsonResponse
     */
    private function updateLocaleEnableDisableAction(Request $request, $state)
    {
        $idx = $request->request->get('data');

        $localeRepository = $this->getDoctrine()->getRepository('AraneumMainBundle:Locale');

        if (!is_array($idx)) {
            return new JsonResponse('Data must be an array');
        }

        $errors = $this->get('validator')->validate($idx, new All([new Regex('/^\d+$/')]));
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors);
        }

        $localeRepository->updateEnabled($idx, $state);

        return new JsonResponse('Success');
    }
}
