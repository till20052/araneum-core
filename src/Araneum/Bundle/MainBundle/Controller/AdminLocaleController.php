<?php

namespace Araneum\Bundle\MainBundle\Controller;


use Araneum\Bundle\MainBundle\Service\DataTable\LocaleDataTableList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminLocaleController extends Controller
{
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