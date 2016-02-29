<?php

namespace Araneum\Bundle\MailBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;

/**
 * Class AdminLocaleControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class AdminMailControllerTest extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->initActionUrl = 'araneum_manage_mails_init';
        $this->gridActionUrl = 'araneum_manage_mails_grid';
    }
}
