<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Base\Tests\Fixtures\Main\ClusterFixtures;
use Araneum\Base\Tests\Fixtures\Main\ComponentFixtures;
use Araneum\Base\Tests\Fixtures\Main\ConnectionFixtures;
use Araneum\Base\Tests\Fixtures\Main\LocaleFixtures;
use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Event\ApplicationEvent;

class ApplicationAdminTest extends BaseAdminController
{
    const TEST_APP_NAME = 'TestApplicationName';
    protected $createRoute = 'admin_araneum_main_application_create';
    protected $updateRoute = 'admin_araneum_main_application_edit';
    protected $deleteRoute = 'admin_araneum_main_application_delete';
    protected $listRoute = 'admin_araneum_main_application_list';

	/**
     * Set of arguments for testCreate method
     *
     * @return array
     */
    public function createDataSource()
    {
	    $client = self::createClient();

        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $cluster = $manager->getRepository('AraneumMainBundle:Cluster')
            ->findOneByName(ClusterFixtures::TEST_CLU_NAME);

        $db = $manager->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(ConnectionFixtures::TEST_CONN_DB_NAME);

        $locale = $manager->getRepository('AraneumMainBundle:Locale')
            ->findOneByName(LocaleFixtures::TEST_LOC_NAME);

        $components = $manager->getRepository('AraneumMainBundle:Component')
            ->findOneByName(ComponentFixtures::TEST_COMP_NAME);

        return [
            'Check simple creation' => [
                [
                    'name' => self::TEST_APP_NAME . '#' . md5(self::TEST_APP_NAME),
                    'domain' => 'test-app-creation.com',
                    'aliases' => 'www.test-app-creation.com, ww2.test-app-creation.com',
                    'public' => true,
                    'enabled' => true,
                    'template' => 'TestApplicationCreationTemplate',
                    'cluster' => $cluster->getId(),
                    'db' => $db->getId(),
                    'locales' => [$locale->getId()],
                    'components' => [$components->getId()],
                ],
                true
            ],
            'Check application unique name' => [
                [
                    'name' => ApplicationFixtures::TEST_APP_NAME,
                    'domain' => 'domain.com',
                    'aliases' => 'www.domain.com, ww2.domain.com',
                    'public' => true,
                    'enabled' => true,
                    'template' => 'template.html',
                    'cluster' => $cluster->getId(),
                    'db' => $db->getId(),
                    'locales' => [$locale->getId()],
                    'components' => [$components->getId()],
                ],
                false
            ],
            'Check domain validator' => [
                [
                    'name' => 'TestApplicationCreation#' . md5(microtime()),
                    'domain' => 'test_app_creation',
                    'public' => true,
                    'enabled' => true,
                    'template' => 'TestApplicationCreationTemplate',
                    'cluster' => $cluster->getId(),
                    'db' => $db->getId(),
                    'locales' => [$locale->getId()],
                    'components' => [$components->getId()],
                ],
                false
            ]
        ];
    }

    /**
     * Set of arguments for testFilter method
     *
     * @return array
     */
    public function filterDataSource()
    {
	    $client = self::createClient();

        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $cluster = $manager->getRepository('AraneumMainBundle:Cluster')
            ->findOneByName(ClusterFixtures::TEST_CLU_NAME);

        $db = $manager->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(ConnectionFixtures::TEST_CONN_DB_NAME);

        $locale = $manager->getRepository('AraneumMainBundle:Locale')
            ->findOneByName(LocaleFixtures::TEST_LOC_NAME);

        $owner = $manager->getRepository('AraneumUserBundle:User')
            ->findOneByEmail(UserFixtures::TEST_USER_EMAIL);

        $application = $manager->getRepository('AraneumMainBundle:Application')
            ->findOneByName(ApplicationFixtures::TEST_APP_NAME);

        $tempApplication = $manager->getRepository('AraneumMainBundle:Application')
            ->findOneByName(ApplicationFixtures::TEST_APP_TEMP_NAME);

        return [
            'Check filter searching application by this application values' => [
                [
                    'filter[cluster][value]' => $cluster->getId(),
                    'filter[name][value]' => ApplicationFixtures::TEST_APP_NAME,
                    'filter[domain][value]' => ApplicationFixtures::TEST_APP_DOMAIN,
                    'filter[db][value]' => $db->getId(),
                    'filter[public][value]' => ApplicationFixtures::TEST_APP_PUBLIC,
                    'filter[enabled][value]' => ApplicationFixtures::TEST_APP_ENABLED,
                    'filter[locales][value]' => $locale->getId(),
                    'filter[owner][value]' => $owner->getId(),
                    'filter[template][value]' => ApplicationFixtures::TEST_APP_TEMPLATE,
                    'filter[createdAt][value][start]' => '01/01/1971',
                    'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400)
                ],
                true,
                $application
            ],
            'Search another application by first filters' => [
                [
                    'filter[cluster][value]' => $cluster->getId(),
                    'filter[name][value]' => ApplicationFixtures::TEST_APP_NAME,
                    'filter[domain][value]' => ApplicationFixtures::TEST_APP_DOMAIN,
                    'filter[db][value]' => $db->getId(),
                    'filter[public][value]' => ApplicationFixtures::TEST_APP_PUBLIC,
                    'filter[enabled][value]' => ApplicationFixtures::TEST_APP_ENABLED,
                    'filter[locales][value]' => $locale->getId(),
                    'filter[owner][value]' => $owner->getId(),
                    'filter[template][value]' => ApplicationFixtures::TEST_APP_TEMPLATE,
                    'filter[createdAt][value][start]' => '01/01/1971',
                    'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400)
                ],
                false,
                $tempApplication
            ]
        ];
    }

    /**
     * Set of arguments for testUpdate method
     *
     * @return array
     */
    public function updateDataSource()
    {
	    $client = self::createClient();

        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $tempApplication = $manager->getRepository('AraneumMainBundle:Application')
            ->findOneByName(ApplicationFixtures::TEST_APP_TEMP_NAME);

        return [
            'Check simple modification' => [
                [
                    'name' => 'TestApplicationModification#' . md5(microtime(true)),
                    'domain' => 'domain.com',
                    'aliases' => 'www.domain.com, ww2.domain.com',
                    'public' => true,
                    'enabled' => true,
                    'template' => 'template.html'
                ],
                true,
                $tempApplication
            ],
            'Check updating name if application with this name exists' => [
                [
                    'name' => ApplicationFixtures::TEST_APP_NAME,
                    'domain' => 'domain.com',
                    'aliases' => 'www.domain.com, ww2.domain.com',
                    'public' => true,
                    'enabled' => true,
                    'template' => 'template.html'
                ],
                false,
                $tempApplication
            ],
            'Set first values of temp application' => [
                [
                    'name' => ApplicationFixtures::TEST_APP_TEMP_NAME,
                    'domain' => ApplicationFixtures::TEST_APP_TEMP_DOMAIN,
                    'aliases' => ApplicationFixtures::TEST_APP_TEMP_ALIASES,
                    'public' => ApplicationFixtures::TEST_APP_TEMP_PUBLIC,
                    'enabled' => ApplicationFixtures::TEST_APP_TEMP_ENABLED,
                    'template' => ApplicationFixtures::TEST_APP_TEMP_TEMPLATE
                ],
                true,
                $tempApplication
            ]
        ];
    }

    /**
     * Return entity for testDelete method
     *
     * @return mixed
     */
    public function deleteDataSource()
    {
	    $client = self::createClient();

        return $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByName(self::TEST_APP_NAME . '#' . md5(self::TEST_APP_NAME));
    }
}