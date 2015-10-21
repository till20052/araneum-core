<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BaseCheckerController extends Controller
{
    public function getIdxElements(Request $request, $adminCode)
    {
        $data = json_decode($request->request->get('data'));
        $idx = $data->idx;
        $allElements = $data->all_elements;

        $admin = $this->container->get('sonata.admin.pool')->getAdminByAdminCode($adminCode);
        $modelManager = $admin->getModelManager();

        if ($allElements) {
            $datagrid = $admin->getDatagrid();
            $datagrid->buildPager();
            $query = $datagrid->getQuery();
            $query->setFirstResult(null);
            $query->setMaxResults(null);
            $result = $modelManager->executeQuery($query);

            foreach ($result as $entity) {
                $idx[] = $entity->getId();
            }
        }

        return $idx;
    }
}
