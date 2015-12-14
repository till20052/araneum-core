<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Service\ApplicationApiHandlerService;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ApplicationApiHandlerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service
 */
class ApplicationApiHandlerTest extends BaseController
{
    const APP_CLASS = 'AraneumMainBundle:Application';

    const API_KEY = '111111111111111';

    protected $manager;

    protected $repository;

    /**
     * Method that called before tests.
     *
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->manager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->manager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(self::APP_CLASS))
            ->will($this->returnValue($this->repository));
    }

    /**
     * Test ApplicationApiHandlerService verifies that returns an array of the desired keys and values
     */
    public function testGet()
    {
        $applicationHandler = new ApplicationApiHandlerService($this->manager);

        $cluster = $this->getMock('Araneum\Bundle\MainBundle\Entity\Cluster');
        $component = $this->getMock('Araneum\Bundle\MainBundle\Entity\Component');
        $owner = $this->getMock('Araneum\Bundle\UserBundle\Entity\User');
        $locale = $this->getMock('Araneum\Bundle\MainBundle\Entity\Locale');
        $db = $this->getMock('Araneum\Bundle\MainBundle\Entity\Connection');

        $application = new Application();
        $application->setId(123);
        $application->setName('testname');
        $application->setType('testtype');
        $application->setAliases('www.testname.test, www2.testname.test');
        $application->setCluster($cluster);
        $application->setDb($db);
        $application->setDomain('testname.test');
        $application->setPublic();
        $application->setEnabled();
        $application->setLocales(new ArrayCollection([$locale]));
        $application->setComponents(new ArrayCollection([$component]));
        $application->setOwner($owner);
        $application->setStatus(1);
        $application->setTemplate('testtemplate');

        $testAppConfig = [
            'id' => 123,
            'name' => 'testname',
            'type' => 'testtype',
            'aliases' => 'www.testname.test, www2.testname.test',
            'cluster' => $cluster,
            'db' => $db,
            'domain' => 'testname.test',
            'public' => true,
            'enabled' => true,
            'locales' => new ArrayCollection([$locale]),
            'components' => new ArrayCollection([$component]),
            'owner' => $owner,
            'status' => 1,
            'template' => 'testtemplate',
        ];

        $this->repository
            ->expects($this->once())
            ->method("findOneBy")
            ->with($this->equalTo(['appKey' => self::API_KEY]))
            ->will($this->returnValue($application));
        $appConfig = $applicationHandler->get(self::API_KEY);

        $this->assertEquals($testAppConfig, $appConfig);
    }

    /**
     * Test ApplicationHandler Exception
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionCode \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
     * @expectedExceptionMessage Not Application found for this appKey
     */
    public function testGetException()
    {
        $applicationHandler = new ApplicationApiHandlerService($this->manager);
        $applicationHandler->get(self::API_KEY);
    }
}
