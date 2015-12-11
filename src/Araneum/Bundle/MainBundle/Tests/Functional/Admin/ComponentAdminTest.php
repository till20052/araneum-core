<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\AbstractBaseAdminController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Base\Tests\Fixtures\Main\ComponentFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;

/**
 * Class ComponentAdminTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class ComponentAdminTestAbstractBaseAdminController extends AbstractBaseAdminController
{
    const TEST_COMP_NAME = 'TestComponentName';
    protected $createRoute = 'admin_araneum_main_component_create';
    protected $updateRoute = 'admin_araneum_main_component_edit';
    protected $deleteRoute = 'admin_araneum_main_component_delete';
    protected $listRoute   = 'admin_araneum_main_component_list';

    /**
     * Set of arguments for testCreate method
     *
     * @return array
     */
    public function createDataSource()
    {
        $client = static::createClient();
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $application = $manager->getRepository('AraneumMainBundle:Application')
            ->findOneByName(ApplicationFixtures::TEST_APP_NAME);

        return [
            'Simple component creation' => [
                [
                    'name' => self::TEST_COMP_NAME.'_'.md5(self::TEST_COMP_NAME),
                    'applications' => $application->getId(),
                    'description' => '',
                    'enabled' => true,
                    'default' => true,
                ],
                true,
            ],
            'Check component unique name' => [
                [
                    'name' => ComponentFixtures::TEST_COMP_NAME,
                    'applications' => $application->getId(),
                    'description' => '',
                    'enabled' => true,
                    'default' => true,
                ],
                false,
            ],
        ];
    }

    /**
     * Set of arguments for testFilter method
     *
     * @return array
     */
    public function filterDataSource()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $fxtComponent = $manager->getRepository('AraneumMainBundle:Component')
            ->findOneByName(ComponentFixtures::TEST_COMP_NAME);

        $tempComponent = $manager->getRepository('AraneumMainBundle:Component')
            ->findOneByName(ComponentFixtures::TEST_COMP_TEMP_NAME);

        $application = $manager->getRepository('AraneumMainBundle:Application')
            ->findOneByName(ApplicationFixtures::TEST_APP_NAME);

        return [
            'try to find first fixture.' => [
                [
                    'filter[name][value]' => ComponentFixtures::TEST_COMP_NAME,
                    'filter[applications][value]' => $application->getId(),
                    'filter[description][value]' => ComponentFixtures::TEST_COMP_DESC,
                    'filter[enabled][value]' => ComponentFixtures::TEST_COMP_ENABLED,
                    'filter[default][value]' => ComponentFixtures::TEST_COMP_DEFAULT,
                    'filter[createdAt][value][start]' => '01/01/1971',
                    'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400),
                ],
                true,
                $fxtComponent,
            ],
            'try to find temp fixture by first fixture values' => [
                [
                    'filter[name][value]' => ComponentFixtures::TEST_COMP_NAME,
                    'filter[applications][value]' => $application->getId(),
                    'filter[description][value]' => ComponentFixtures::TEST_COMP_DESC,
                    'filter[enabled][value]' => ComponentFixtures::TEST_COMP_ENABLED,
                    'filter[default][value]' => ComponentFixtures::TEST_COMP_DEFAULT,
                    'filter[createdAt][value][start]' => '01/01/1971',
                    'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400),
                ],
                false,
                $tempComponent,
            ],
        ];
    }

    /**
     * Set of arguments for testUpdate method
     *
     * @return array
     */
    public function updateDataSource()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $tempComponent = $manager->getRepository('AraneumMainBundle:Component')
            ->findOneByName(ComponentFixtures::TEST_COMP_TEMP_NAME);

        $application = $manager->getRepository('AraneumMainBundle:Application')
            ->findOneByName(ApplicationFixtures::TEST_APP_NAME);

        return [
            'Check simple modification' => [
                [
                    'applications' => $application->getId(),
                    'name' => 'TestCheckModification_'.md5(microtime(true)),
                    'description' => '',
                    'options][0][key' => 'test_option_update_key_1',
                    'options][0][value' => 'test_option_update_value_1',
                    'enabled' => true,
                    'default' => true,
                ],
                true,
                $tempComponent,
            ],
            'Check update temp component name if component with this name already exists' => [
                [
                    // Check component unique name
                    'name' => ComponentFixtures::TEST_COMP_NAME,
                    'applications' => $application->getId(),
                    'description' => '',
                    'enabled' => true,
                    'default' => true,
                ],
                false,
                $tempComponent,
            ],
            'Set first values of temp component' => [
                [
                    'name' => ComponentFixtures::TEST_COMP_TEMP_NAME,
                    'applications' => $application->getId(),
                    'description' => ComponentFixtures::TEST_COMP_TEMP_DESC,
                    'enabled' => ComponentFixtures::TEST_COMP_TEMP_ENABLED,
                    'default' => ComponentFixtures::TEST_COMP_TEMP_DEFAULT,
                ],
                true,
                $tempComponent,
            ],
        ];
    }

    /**
     * Return entity for testDelete method
     *
     * @return mixed
     */
    public function deleteDataSource()
    {
        $client = static::createClient();

        return $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Component')
            ->findOneByName(self::TEST_COMP_NAME.'_'.md5(self::TEST_COMP_NAME));
    }
}
