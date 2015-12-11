<?php

namespace Araneum\Base\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminBaseController
 *
 * @package Araneum\Base\Controller
 */
class AdminBaseController extends Controller
{
    public $admin;

    /**
     * Get ids elements for check status
     *
     * @param         $request
     * @param         $adminCode
     * @return array
     */
    protected function getIdxElements($request, $adminCode)
    {
        $data = (array) $request;
        $idx = $data['idx'];
        $allElements = $data['all_elements'];

        if ($allElements && count($idx) == 0) {
            $this->admin = $this->get('sonata.admin.pool')->getAdminByAdminCode($adminCode);
            $modelManager = $this->admin->getModelManager();
            $datagrid = $this->admin->getDatagrid();
            $datagrid->buildPager();
            $query = $datagrid->getQuery();
            $query->setFirstResult(null);
            $query->setMaxResults(null);
            $result = $modelManager->executeQuery($query);

            foreach ($result as $entity) {
                if ($entity->isEnabled()) {
                    $idx[] = $entity->getId();
                }
            }
        }

        return $idx;
    }
}
