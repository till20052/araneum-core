<?php

namespace Araneum\Bundle\MainBundle\Controller;


use Araneum\Bundle\MainBundle\Service\DataTable\LocaleDataTableList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminLocaleController extends Controller
{
	/**
	 * @Route("/admin/grid/locale.json", name="araneum_admin_grid_locale")
	 * @Route("/locale/grid/locale.json", name="araneum_admin_grid_locale-2")
	 * @return JsonResponse
	 */
	public function getGridAction()
	{
		return $this
				->get('araneum_datatable.factory')
				->create(new LocaleDataTableList())->execute();
	}
}