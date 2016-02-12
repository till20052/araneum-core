<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;

/**
 * Class AdminLocaleControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class AdminLocaleControllerTest extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->initActionUrl = 'araneum_manage_locales_init';
        $this->gridActionUrl = 'araneum_manage_locales_grid';
    }
}
