<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 24.09.15
 * Time: 16:40
 */

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Tests\Functional\Utils\Data;
use Symfony;


class ClusterAdminTest extends BaseController
{

    private $prefix;
    static $client;
    static $connId;
    const FILTER_CONNECTION = ['name' => 'functionalTestConnection'];
    const FILTER_CLUSTER_NAME = ['name' => 'clusterTestName'];
    const TYPE_MULTIPLE = 2;
    const STATUS_ONLINE = 1;


    /**
     * Setup befor class
     */
    public static function setUpBeforeClass(){
        $client = static::createClient();
        Data\ManageTestEntities::deleteClusterByName($client->getContainer()->get('doctrine.orm.default_entity_manager'), self::FILTER_CLUSTER_NAME);
        $conn = Data\ManageTestEntities::CreateConnection($client->getContainer()->get('doctrine.orm.default_entity_manager'), self::FILTER_CONNECTION);
        self::$connId = $conn->getId();
    }

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
     * @dataProvider saveProvider
     * @param $name
     * @param $type
     * @param $status
     * @param $enabled
     * @param $expects
     * @runInSeparateProcess
     */
    public function testCreateAction($name, $type, $status, $enabled, $expects){

        $client = $this->createAdminAuthorizedClient();

        $router = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('admin_araneum_main_cluster_create'));

        $form = $crawler->selectButton('btn_create_and_edit')->form();

        $this->getFormPrefix($form);

        $prefix = $this->prefix;

        $scrawler = $client->submit($form, [
                $prefix.'[name]' => $name['name'],
                $prefix.'[host]' => self::$connId,
                $prefix.'[type]' => $type,
                $prefix.'[status]' =>$status,
                $prefix.'[enabled]' => $enabled,
        ]);
        $this->assertEquals(count($crawler->filter('.alert-danger'))==0, $expects);

        $client = static::createClient();
        Data\ManageTestEntities::deleteClusterByName($client->getContainer()->get('doctrine.orm.default_entity_manager'), self::FILTER_CLUSTER_NAME);
    }


    /**
     * @runInSeparateProcess
     */
    public function testUpdateAction(){

        $client = static::createClient();

        $cluster = Data\ManageTestEntities::CreateCluster($client->getContainer()->get('doctrine.orm.default_entity_manager'), self::FILTER_CLUSTER_NAME, self::FILTER_CONNECTION);

        $clusterId = $cluster->getId();

        $client = $this->createAdminAuthorizedClient();

        $router = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('admin_araneum_main_cluster_edit', array('id' => $clusterId)));

        $form = $crawler->selectButton('btn_update_and_edit')->form();

        $this->getFormPrefix($form);

        $prefix = $this->prefix;

        $name = self::FILTER_CLUSTER_NAME;

        $scrawler = $client->submit($form, [
            $prefix.'[name]' => $name['name'],
            $prefix.'[type]' => Cluster::TYPE_SINGLE,
            $prefix.'[status]' =>Cluster::STATUS_OFFLINE,
            $prefix.'[enabled]' => false,
        ]);
        $this->assertEquals(count($crawler->filter('.alert-danger'))==0, true);

    }


    /**
     * Save provider method for @dataProvider
     *
     * @return array
     */
    public function saveProvider(){
        return [
            [
                self::FILTER_CLUSTER_NAME,
                2,
                true,
                self::STATUS_ONLINE,
                true
            ],
            [
                self::FILTER_CLUSTER_NAME,
                self::TYPE_MULTIPLE,
                '1',
                self::STATUS_ONLINE,
                true
            ],
            [
                self::FILTER_CLUSTER_NAME,
                self::TYPE_MULTIPLE,
                true,
                '1',
                true
            ],
            [
                self::FILTER_CLUSTER_NAME,
                self::TYPE_MULTIPLE,
                true,
                self::STATUS_ONLINE,
                true
            ]
        ];

    }

    /**
     * Tear down After class
     *
     * clean test data Cluster
     */
    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        Data\ManageTestEntities::deleteClusterByName($client->getContainer()->get('doctrine.orm.default_entity_manager'), self::FILTER_CLUSTER_NAME);
    }
}