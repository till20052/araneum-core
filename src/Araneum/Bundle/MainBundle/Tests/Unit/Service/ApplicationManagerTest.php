<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ApplicationManagerServiceTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service
 */
class ApplicationManagerServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $doctrineMock;
    protected $repositoryMock;
    protected $applicationManager;
    protected $repository;
    protected $appKey = ApplicationFixtures::TEST_APP_APP_KEY;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->doctrineMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repositoryMock = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->doctrineMock->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AraneumMainBundle:Application'))
            ->will($this->returnValue($this->repositoryMock));

        $this->applicationManager = new ApplicationManagerService($this->doctrineMock);
    }

    /**
     * Test find oneOr404 method
     * assert return good application
     */
    public function testFindOne()
    {
        $application = new Application();
        $this->repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(['appKey' => $this->appKey]))
            ->will($this->returnValue($application));

        $actual = $this->applicationManager->findOneOr404(['appKey' => $this->appKey]);
        $this->assertEquals($application, $actual);
    }

    /**
     * Test find oneOr404
     * assert return bad application and trow exception
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionCode \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
     * @expectedExceptionMessage Not Application found for this appKey
     */
    public function testGetException()
    {
        $application = new Application();
        $this->repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(['appKey' => $this->appKey]))
            ->will($this->returnValue(null));

        $actual = $this->applicationManager->findOneOr404(['appKey' => $this->appKey]);
        $this->assertEquals($application, $actual);
    }
}
