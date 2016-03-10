<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;

/**
 * Class AdminClusterControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class AdminClusterControllerTest extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->initActionUrl = 'araneum_manage_clusters_init';
        $this->gridActionUrl = 'araneum_manage_clusters_grid';
    }
}
