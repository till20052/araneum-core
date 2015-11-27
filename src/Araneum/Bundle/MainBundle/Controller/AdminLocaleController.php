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
	 * @Route("/admin/locales/init.json", name="araneum-admin-locales-init")
	 * @return JsonResponse
	 */
	public function initAction()
	{
		return new JsonResponse(
			[
				'datatable' => [
					'columns' => $this->get('araneum_datatable.factory')
						->create(new LocaleDataTableList())
						->getColumns()
				]
			]
		);
	}

	/**
	 * Server/client datatable communication
	 *
	 * @Route("/admin/locales/datatable.json", name="araneum-admin-locales-datatable")
	 * @return JsonResponse
	 */
	public function datatableAction()
	{
		return $this
			->get('araneum_datatable.factory')
			->create(new LocaleDataTableList())->execute();
	}
}