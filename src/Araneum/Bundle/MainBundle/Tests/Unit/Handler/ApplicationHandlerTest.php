<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Handler;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Handler\ApplicationHandler;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplicationHandlerTest extends BaseController
{
    const APP_CLASS = 'Araneum\Bundle\MainBundle\Entity\Application';

    const API_KEY = '111111111111111';

    protected $manager;

    protected $repository;

    /**
     * Method that called before tests
     *
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->manager = $this
            ->getMockBuilder('Doctrine\Common\Persistence\ObjectManager')
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
     * Test ApplicationHandler verifies that returns an array of the desired keys and values
     */
    public function testGet()
    {
        $applicationHandler = new ApplicationHandler($this->manager, self::APP_CLASS);

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
        $application->setLocale($locale);
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
            'locale' => $locale,
            'components' => new ArrayCollection([$component]),
            'owner' => $owner,
            'status' => 1,
            'template' => 'testtemplate'
        ];

        $this->repository
            ->expects($this->once())
            ->method("findOneBy")
            ->with($this->equalTo(['apiKey' => self::API_KEY]))
            ->will($this->returnValue($application));
        $appConfig = $applicationHandler->get(self::API_KEY);

        $this->assertEquals($testAppConfig, $appConfig);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionCode \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
     * @expectedExceptionMessage Not Application found for this apiKey
     */
    public function testGetException()
    {
        $applicationHandler = new ApplicationHandler($this->manager, self::APP_CLASS);
        $applicationHandler->get(self::API_KEY);
        throw new NotFoundHttpException('Not Application found for this apiKey');
    }
}