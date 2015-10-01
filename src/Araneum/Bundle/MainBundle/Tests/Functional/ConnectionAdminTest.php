<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Araneum\Base\Tests\Controller\BaseAdminController;

class ConnectionAdminTest extends BaseAdminController
{
    protected $listRoute = 'admin_araneum_main_connection_list';
    protected $createRoute = 'admin_araneum_main_connection_create';
    protected $updateRoute = 'admin_araneum_main_connection_edit';
    protected $deleteRoute = 'admin_araneum_main_connection_delete';

    /**
     * @inheritdoc
     */
    public function filterDataSource()
    {
        $connection = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneBy(['name' => 'TestDbConnection']);

        return
            [
                [
                    'Filter with good data' => [
                        'filter[type][type]' => 1,
                        'filter[name][value]' => 'TestDbConnection',
                        'filter[host][value]' => '192.168.1.200',
                        'filter[port][value]' => 4321,
                        'filter[userName][value]' => 'TestDbConnectionUserName',
                        'filter[enabled][value]' => true,
                        'filter[createdAt][value][start]' => '01/01/1970',
                        'filter[createdAt][value][end]' => '01/01/2040',
                        'filter[updatedAt][value][start]' => '01/01/1970',
                        'filter[updatedAt][value][end]' => '01/01/2040'
                    ],
                    true,
                    $connection,
                    'Filter with bad data' => [
                        'filter[type][type]' => 1,
                        'filter[name][value]' => 'TestDbConnection',
                        'filter[host][value]' => '192.168.1.200',
                        'filter[port][value]' => 4321,
                        'filter[userName][value]' => 'TestDbConnectionUserName',
                        'filter[enabled][value]' => false,
                        'filter[createdAt][value][start]' => '01/01/1970',
                        'filter[createdAt][value][end]' => '01/01/2040',
                        'filter[updatedAt][value][start]' => '01/01/1970',
                        'filter[updatedAt][value][end]' => '01/01/2040'
                    ],
                    false,
                    $connection
                ]
            ];
    }

    /**
     * @inheritdoc
     */
    public function createDataSource()
    {
        return
            [
                [
                    'Good create' => [
                        'type' => 1,
                        'name' => 'functionalTestConnection',
                        'host' => 'testHost',
                        'port' => 1111,
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    true,
                    'Unique create test' => [
                        'type' => 1,
                        'name' => 'functionalTestConnection',
                        'host' => 'testHost',
                        'port' => 1111,
                        'user' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    false,
                    'Port must be integer' => [
                        'type' => 1,
                        'name' => 'testNameAnother',
                        'host' => 'testHost',
                        'port' => 'string',
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    false,
                    'Min length of password' => [
                        'type' => 1,
                        'name' => 'testNameAnotherNew',
                        'host' => 'testHost',
                        'port' => 1111,
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'no'
                    ],
                    false
                ]
            ];
    }

    /**
     * @inheritdoc
     */
    public function updateDataSource()
    {
        $connection = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneBy(['name' => 'TestConnection']);

        return
            [
                [
                    'Update without changes' => [
                        'type' => 1,
                        'name' => 'TestConnection',
                        'host' => 'testHost',
                        'port' => 1111,
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    true,
                    $connection,
                    'Update name' => [
                        'type' => 2,
                        'name' => 'testNameAnother',
                        'host' => 'testHost',
                        'port' => 'string',
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    false,
                    $connection,
                    'Update with min length password' => [
                        'type' => 3,
                        'name' => 'testNameAnotherNew',
                        'host' => 'testHost',
                        'port' => 1111,
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'no'
                    ],
                    false,
                    $connection
                ]
            ];
    }

    /**
     * @inheritdoc
     */
    public function deleteDataSource()
    {
        $connection = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneBy(['name' => 'functionalTestConnection']);

        return $connection;
    }
}