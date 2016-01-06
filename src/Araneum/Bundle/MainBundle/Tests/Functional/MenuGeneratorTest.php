<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 06.11.15
 * Time: 9:34
 */

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Araneum\Base\Tests\Controller\BaseController;

/**
 * Class MenuGeneratorTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional
 */
class MenuGeneratorTest extends BaseController
{
    /**
     *  Test left menu
     * @runInSeparateProcess
     */
    public function testLeftMenu()
    {

        $client = static::createAdminAuthorizedClient('admin');

        $client->request(
            'GET',
            '/manage/menu.json'
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
