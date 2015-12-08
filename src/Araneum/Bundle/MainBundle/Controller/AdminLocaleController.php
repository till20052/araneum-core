<?php

namespace Araneum\Bundle\MainBundle\Controller;


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

class AdminLocaleController extends AdminBaseController
{

	/**
	 * Delete locales one or many
	 *
	 * @param Request $request
	 * @return Response
	 * @Route("/manage/admin/locale/delete", condition="request.request.get('data') matches '/delete/'"
	 *     ,name="araneum_main_admin_locale_delete")
	 */
	public function deleteAction(Request $request){
		$idx = $this->getIdxElements(json_decode($request->request->get('data')), 'araneum.main.admin.locale');
	}

	/**
	 * Enable locales one or many
	 *
	 * @param Request $request
	 * @return Response
	 * @Route("/manage/admin/locale/enable", condition="request.request.get('data') matches '/enable/'"
	 *     ,name="araneum_main_admin_locale_enable")
	 */
	public function enableAction(Request $request){

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
	public function disableAction(Request $request){

		return $this->updateLocaleEnableDisableAction($request, false);
	}

	/**
	 * Update locale state
	 *
	 * @param Request $request
	 * @param bool $state
	 * @return JsonResponse
	 */
	private function updateLocaleEnableDisableAction(Request $request, $state){
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