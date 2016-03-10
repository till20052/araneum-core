<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Service\ClusterApiHandlerService;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ClusterApiHandlerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service
 */
class ClusterApiHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var ClusterApiHandlerService
     */
    private $handler;

    /**
     * @var int
     */
    private $clusterId = 777;

    /**
     * Initialize requirement services
     */
    protected function setUp()
    {
        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Repository\ClusterRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = new ClusterApiHandlerService($entityManager);

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Cluster'))
            ->will($this->returnValue($this->repository));
    }

    /**
     * Test ClusterApiHandlerService to return correct structure of applications configs
     */
    public function testGetApplicationsConfigsList()
    {
        $cluster = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Cluster');
        $cluster->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($this->clusterId));

        $db = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Connection');
        $db->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('db_name'));
        $db->expects($this->once())
            ->method('getHost')
            ->will($this->returnValue('db.host.dev'));
        $db->expects($this->once())
            ->method('getPort')
            ->will($this->returnValue(5432));
        $db->expects($this->once())
            ->method('getUserName')
            ->will($this->returnValue('db_username'));
        $db->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue('db_password'));

        $locale = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Locale');
        $locale->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('locale_name'));
        $locale->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue('en'));
        $locale->expects($this->once())
            ->method('getOrientation')
            ->will($this->returnValue(Locale::ORIENT_LFT_TO_RGT));
        $locale->expects($this->once())
            ->method('getEncoding')
            ->will($this->returnValue('UTF-8'));

        $component = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Component');
        $component->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('component_name'));
        $component->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(['option_key' => 'option_value']));

        $application = $this->getMock('\Araneum\Bundle\MainBundle\Entity\Application');
        $application->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('domain.com'));
        $application->expects($this->once())
            ->method('getAliases')
            ->will($this->returnValue('www.domain.com, dev.domain.com'));
        $application->expects($this->once())
            ->method('getTemplate')
            ->will($this->returnValue('template'));
        $application->expects($this->once())
            ->method('getAppKey')
            ->will($this->returnValue(($appKey = md5(microtime(true)))));
        $application->expects($this->atLeastOnce())
            ->method('getCluster')
            ->will($this->returnValue($cluster));
        $application->expects($this->atLeastOnce())
            ->method('getDb')
            ->will($this->returnValue($db));
        $application->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue(new ArrayCollection([$locale])));
        $application->expects($this->once())
            ->method('getComponents')
            ->will($this->returnValue(new ArrayCollection([$component])));

        $cluster->expects($this->once())
            ->method('getApplications')
            ->will($this->returnValue(new ArrayCollection([$application])));

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($this->clusterId))
            ->will($this->returnValue($cluster));

        $expectedStructure = [
            [
                'domain' => 'domain.com',
                'aliases' => 'www.domain.com, dev.domain.com',
                'template' => 'template',
                'app_key' => $appKey,
                'cluster' => [
                    'id' => $this->clusterId,
                ],
                'db' => [
                    'name' => 'db_name',
                    'host' => 'db.host.dev',
                    'port' => 5432,
                    'user_name' => 'db_username',
                    'password' => 'db_password',
                ],
                'locales' => [
                    [
                        'name' => 'locale_name',
                        'locale' => 'en',
                        'orientation' => Locale::ORIENT_LFT_TO_RGT,
                        'encoding' => 'UTF-8',
                    ],
                ],
                'components' => [
                    [
                        'name' => 'component_name',
                        'options' => ['option_key' => 'option_value'],
                    ],
                ],
            ],
        ];

        $this->assertEquals(
            $expectedStructure,
            $this->handler->getApplicationsConfigsList($this->clusterId)
        );
    }

    /**
     * Test ClusterApiHandlerService in case if cluster not exists
     */
    public function testGetApplicationsConfigsListByNotExistingCluster()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($this->clusterId))
            ->will($this->returnValue(false));

        $this->assertEquals(
            false,
            $this->handler->getApplicationsConfigsList($this->clusterId)
        );
    }
}
