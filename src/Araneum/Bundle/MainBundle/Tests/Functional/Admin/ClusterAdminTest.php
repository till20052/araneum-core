<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 24.09.15
 * Time: 16:40
 */

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Araneum\Bundle\MainBundle\Tests\Functional\Utils\Data;



class ClusterAdminTest extends BaseController
{

    private $prefix;
    const FILTER = ['name' => 'functionalTestConnection'];
    const TYPE_MULTIPLE = 2;
    const STATUS_ONLINE = 1;

    /**
     * @beforeClass
     * @param $form
     * @return mixed
     */
    private function getFormPrefix($form)
    {
        $this->prefix = key(array_slice($form->getPhpValues(), 1, 1));
    }

    /**
     * Test for create cluster
     *
     * @dataProvider saveProvider
     * @param $name
     * @param $host
     * @param $type
     * @param $enabled
     * @param $type
     * @param $expects
     * @runInSeparateProcess
     */
    public function testCreateAction($name, $host, $type, $enabled, $type, $expects){
        $client = $this->createAdminAuthorizedClient();

        $connection = Data\CreateTestEntities::CreateConnection($client->getContainer()->get('doctrine.orm.default_entity_manager'), self::FILTER);

        $crawler = $client->request('GET', '/en/admin/araneum/main/cluster/create');

        $form = $crawler->selectButton('btn_create_and_list')->form();

        $this->getFormPrefix($form);

        $prefix = $this->prefix;

        $scrawler = $client->submit($form, [

        ]);
    }


    /**
     * Save provider method for @dataProvider
     *
     * @return array
     */
    protected function saveProvider(){
        return [
            [
                'testName',
                'testHost',
                2222222,
                true,
                self::STATUS_ONLINE,
                false
            ],
            [
                'testName',
                'testHost',
                self::TYPE_MULTIPLE,
                '3',
                self::STATUS_ONLINE,
                false
            ],
            [
                'testName',
                'testHost',
                self::TYPE_MULTIPLE,
                true,
                '44',
                false
            ],
            [
                'testName',
                'testHost',
                self::TYPE_MULTIPLE,
                true,
                self::STATUS_ONLINE,
                true
            ],
            [
                'testName',
                'testHost',
                self::TYPE_MULTIPLE,
                true,
                self::STATUS_ONLINE,
                false
            ],
        ];

    }
}