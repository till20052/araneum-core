<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminUserControllerTest
 *
 * @package Araneum\Bundle\UserBundle\Tests\Functional\Controller
 */
class AdminUserControllerTest extends BaseController
{
    /**
     * Test for set
     *
     * @runInSeparateProcess
     * @return bool
     */
    public function testSettingsSet()
    {
        return true;
    }
}
