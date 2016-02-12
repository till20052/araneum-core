<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;

/**
 * Class AdminApplicationControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class AdminApplicationControllerTest extends BaseAdminController
{
    public function setUp()
    {
        parent::setUp();

        $this->initActionUrl = 'araneum_manage_applications_init';
        $this->gridActionUrl = 'araneum_manage_applications_grid';
    }
}
