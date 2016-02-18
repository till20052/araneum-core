<?php
namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Controller\AdminApplicationController;
use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Guzzle\Service\Description\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\Dump\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\All;
use Araneum\Base\Tests\Controller\BaseAdminController;

/**
 * Class AdminApplicationControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class AdminApplicationControllerTest extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->initActionUrl = 'araneum_manage_applications_init';
        $this->gridActionUrl = 'araneum_manage_applications_grid';
    }
}
