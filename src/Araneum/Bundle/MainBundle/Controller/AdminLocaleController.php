<?php

namespace Araneum\Bundle\MainBundle\Controller;


use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Service\Actions\LocaleActions;
use Araneum\Bundle\MainBundle\Service\DataTable\LocaleDataTableList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Araneum\Base\Controller\AdminBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Expression\Regex;
use Symfony\Component\Validator\Constraints\All;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminLocaleController extends AdminBaseController
{
    /**
     * Get locale by id
     *
     * @ApiDoc(
     *   resource = "Locale",
     *   section = "MainBundle",
     *   description = "Gets a locale",
     *   output = "Araneum\Bundle\MainBundle\Entity\Locale",
     *   statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Locale not found"
     *   },
     *   requirements = {
     *      {
     *          "name" = "_format",
     *          "dataType" = "json",
     *          "description" = "Output format must be json"
     *      }
     *   },
     *   parameters={
     *      {"name"="id", "dataType"="string", "required"=true}
     *   },
     *   tags={"ApplicationApi"}
     * )
     *
     * @Rest\Get(
     *      "/manage/admin/locale/{id}",
     *      name="araneum_admin_main_locale_get",
     *      defaults={"_format"="json", "_locale"="en"}
     * )
     *
     * @Rest\View()
     * @param $id
     * @return JsonResponse
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
     * Save locale
     *
     * @ApiDoc(
     *   resource = "Locale",
     *   section = "MainBundle",
     *   description = "Save a locale",
     *   output = "Araneum\Bundle\MainBundle\Entity\Locale",
     *   statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Locale not found"
     *   },
     *   requirements = {
     *      {
     *          "name" = "_format",
     *          "dataType" = "json",
     *          "description" = "Output format must be json"
     *      }
     *   },
     *   parameters={
     *      {"name"="id", "dataType"="string", "required"=true, "description"="Searching Application by this parameter"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Searching Customer by this parameter"},
     *      {"name"="current_password", "dataType"="string", "required"=true, "description"="Customer current password"},
     *      {"name"="new_password", "dataType"="string", "required"=true, "description"="Customer new password"}
     *   },
     *   tags={"ApplicationApi"}
     * )
     *
     * @Rest\Get(
     *      "/manage/admin/locale/save",
     *      name="araneum_admin_main_locale_get",
     *      defaults={"_format"="json", "_locale"="en"}
     * )
     *
     * @Rest\View()

     *
     *
     * @Route("/manage/admin/locale/save", name="araneum_admin_main_locale_post")
     * @Method("POST")
     *
     * @param Request $request
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
                        'id' => $locale->getId()
                    ],
                    $code
                );
            } else {

                return new JsonResponse(
                    ['message' => (string)$form->getErrors(true, false)],
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
     * Create locale Entity
     * @param Request $request
     * @return Response
     * @Route("/manage/admin/locale/create", condition="request.request.get('data') matches '/create/'"
     *     ,name="araneum_main_admin_locale_create")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->request->get('data'));

        $locale = new Locale();

        $locale->setName($data['name'])
            ->setEncoding($data['encoding'])
            ->setLocale($data['locale'])
            ->setOrientation($data['orientation'])
            ->setEnabled(true);

        $em->persist($locale);
        $em->flush();

        return new JsonResponse('success');
    }


    /**
     * Delete locales one or many
     *
     * @param Request $request
     * @return Response
     * @Route("/manage/admin/locale/delete", condition="request.request.get('data') matches '/delete/'"
     *     ,name="araneum_main_admin_locale_delete")
     */
    public function deleteAction(Request $request)
    {
        $idx = $this->getIdxElements(json_decode($request->request->get('data')), 'araneum.main.admin.locale');
        $localeRepository = $this->getDoctrine()->getRepository('AraneumMainBundle:Locale');

        if (is_array($idx) && count($idx) > 0) {
            $localeRepository->delete($idx);
        }

        return new JsonResponse('success');
    }

    /**
     * Enable locales one or many
     *
     * @param Request $request
     * @return Response
     * @Route("/manage/admin/locale/enable", condition="request.request.get('data') matches '/enable/'"
     *     ,name="araneum_main_admin_locale_enable")
     */
    public function enableAction(Request $request)
    {

        return $this->updateLocaleEnableDisableAction($request, true);
    }

    /**
     * Disable locales one or many
     *
     * @param Request $request
     * @return Response
     * @Route("/manage/admin/locale/disable", condition="request.request.get('data') matches '/disable/'"
     *     ,name="araneum_main_admin_locale_disable")
     */
    public function disableAction(Request $request)
    {

        return $this->updateLocaleEnableDisableAction($request, false);
    }

    /**
     * Update locale state
     *
     * @param Request $request
     * @param bool $state
     * @return JsonResponse
     */
    private function updateLocaleEnableDisableAction(Request $request, $state)
    {
        $idx = $this->getIdxElements(json_decode($request->request->get('data')), 'araneum.main.admin.locale');

        $localeRepository = $this->getDoctrine()->getRepository('AraneumMainBundle:Locale');

        if (!is_array($idx)) {
            return new JsonResponse('data must be an array');
        }

        $errors = $this->get('validator')->validate($idx, new All([new Regex('/^\d+$/')]));
        if (count($errors) > 0) {
            return new JsonResponse((string)$errors);
        }

        $localeRepository->updateEnabled($idx, $state);

        return new JsonResponse('success');
    }

    /**
     * Locales module initialization
     *
     * @Route("/manage/locales/init.json", name="araneum_manage_locales_init")
     * @return JsonResponse
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
     * @Route("/manage/locales/datatable.json", name="araneum_manage_locales_grid")
     * @return JsonResponse
     */
    public function datatableAction()
    {
        return $this
            ->get('araneum_datatable.factory')
            ->create(new LocaleDataTableList($this->container))
            ->execute();
    }
}