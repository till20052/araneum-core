<?php

namespace Araneum\Bundle\MainBundle\Tests\Maintenance;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MaintenanceTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Maintenance
 */
class MaintenanceTest extends WebTestCase
{

    /**
     * Test is maintenance mode enabled without errors
     */
    public function testMaintenanceOn503Status()
    {
        $client = $this->createClient();
        $container = $client->getContainer();

        $app = new Application($container->get('kernel'));
        $app->setAutoExit(false);
        $app->run(
            new ArrayInput(
                [
                    'command' => 'lexik:maintenance:lock',
                    '-n' => true,
                ]
            ),
            new NullOutput()
        );

        $crawler = $client->request(
            'GET',
            $container->get('router')->generate('fos_user_security_login')
        );

        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $client->getResponse()->getStatusCode());
    }

    /**
     * Test is maintenance mode disabled without errors
     *
     *
     */
    public function testMaintenanceOffNot503Status()
    {
        $client = $this->createClient();
        $container = $client->getContainer();

        $app = new Application($container->get('kernel'));
        $app->setAutoExit(false);
        $app->run(
            new ArrayInput(
                [
                    'command' => 'lexik:maintenance:unlock',
                    '-n' => true,
                ]
            ),
            new NullOutput()
        );

        $crawler = $client->request(
            'GET',
            $container->get('router')->generate('fos_user_security_login')
        );

        $this->assertNotEquals(Response::HTTP_SERVICE_UNAVAILABLE, $client->getResponse()->getStatusCode());
    }
}
