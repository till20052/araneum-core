<?php

namespace Araneum\Bundle\AgentBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;

/**
 * Class AdminLocaleControllerTest
 *
 * @package Araneum\Bundle\AgentBundle\Tests\Functional\Admin
 */
class AdminLeadControllerTest extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->initActionUrl = 'araneum_manage_lead_init';
        $this->gridActionUrl = 'araneum_manage_leads_grid';
    }
}
