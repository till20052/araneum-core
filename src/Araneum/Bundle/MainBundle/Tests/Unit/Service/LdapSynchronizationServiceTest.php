<?php
namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Bundle\UserBundle\Entity\User;
use Araneum\Bundle\UserBundle\Repository\UserRepository;
use Araneum\Bundle\UserBundle\Entity\UserLdapLog;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Araneum\Bundle\MainBundle\Service\LdapSynchronizationService;
use FR3D\LdapBundle\Driver\LdapDriverInterface;
use \Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use FR3D\LdapBundle\Driver\ZendLdapDriver;

/**
 * Class LdapSynchronizationServiceTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service
 */
class LdapSynchronizationServiceTest extends WebTestCase
{
    protected static $entries = [
        [
            'displayname' => ['Test Test'],
            'dn' => 'uid=test,cn=users,cn=accounts,dc=office,dc=dev',
            'krblastpwdchange' => ['20151022114844Z'],
            'mail' => ['test@test.dev'],
            'uid' => ['test']
        ]
    ];
    protected static $ldapParams = [
        'baseDn' => 'cn=accounts,dc=office,dc=dev',
        'filter' => '(&(objectclass=Person))',
        'attributes' => [
            ['ldap_attr' => 'uid', 'user_method' => 'setUsername'],
            ['ldap_attr' => 'displayname', 'user_method' => 'setFullName'],
            ['ldap_attr' => 'mail', 'user_method' => 'setEmail'],
            ['ldap_attr' => 'mail', 'user_method' => 'setEmailCanonical'],
            ['ldap_attr' => 'krblastpwdchange', 'user_method' => 'setLastChangeLdapPass'],
        ]
    ];

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * @var LdapSynchronizationService
     */
    private $service;

    /**
     * @var Doctrine
     */
    private $doctrine;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var ZendLdapDriver
     */
    private $driver;

    /**
     * @var EncoderFactory
     */
    private $encoderFactory;

    public function testRunSynchronization() {
        $this->getMockEntityUser();
        $userRepository = $this->getMockUserRepository();
        $this->doctrine = $this->getMockDoctrine(array($userRepository));
        $this->container = $this->getContainer();

        $this->service = new LdapSynchronizationService(
            $this->container,
            $this->encoderFactory,
            $this->driver,
            $this->entityManager,
            self::$ldapParams
        );

        $result = $this->service->runSynchronization();
        $this->assertInternalType('array',$result);
        $this->assertEquals(2,count($result));
        $this->assertEquals(['sitem' => 1, 'uitem' => 0],$result);
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->driver = $this->getMockZendLdapDriver();
        $this->entityManager = $this->mockEntityManager();
        $this->encoderFactory = $this->getMockEncoderFactory();
    }

    /**
     * Mock EntityUser
     * @return EntityUser
     */
    private function getMockEntityUser()
    {
        $this->getMockBuilder('Araneum\Bundle\UserBundle\Entity\User')
            ->setConstructorArgs( array( 'User') )
            ->getMock();
    }

    /**
     * Mock User Repository
     * @return UserRepository
     */
    private function getMockUserRepository()
    {
        $userRepository = $this->getMockBuilder('Araneum\Bundle\UserBundle\Repository\UserRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $userRepository->expects($this->any())
            ->method('isLdapUser')
            ->will($this->returnValue(true));
        $userRepository->expects($this->any())
            ->method('setAllLdapUsersStatusOld')
            ->will($this->returnValue(true));
        $userRepository->expects($this->any())
            ->method('clearOldLdapUsers')
            ->will($this->returnValue(true));

        return $userRepository;
    }

    /**
     * Mock EntityManager
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockEntityManager()
    {
        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())
            ->method('persist');
        $entityManager->expects($this->any())
            ->method('flush');

        return $entityManager;
    }

    /**
     * Mock ObjectManager
     * @param array $setRepository
     * @return ObjectManager
     */
    private function getMockDoctrine($setRepository)
    {
        $mockManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        foreach ($setRepository as $item) {
            $mockManager->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValue($item));
        }
        $doctrine = $this->getMock('Doctrine', array('getManager', 'getEntityManager'));
        $doctrine->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($mockManager));
        $doctrine->expects($this->any())
            ->method('getEntityManager')
            ->will($this->returnValue($this->mockEntityManager()));

        return $doctrine;
    }

    /**
     * Mock EncoderFactory
     * @return EncoderFactoryInterface
     */
    private function getMockEncoderFactory()
    {
        $encoderFactory = $this->getMock(EncoderFactoryInterface::class);

        return $encoderFactory;
    }

    /**
     * MockZendLdapDriver
     * @return ZendLdapDriver
     */
    private function getMockZendLdapDriver()
    {
        $driver = $this->getMockBuilder('FR3D\LdapBundle\Driver\ZendLdapDriver')
            ->disableOriginalConstructor()
            ->getMock();
        $driver->expects($this->any())
            ->method('search')
            ->will($this->returnValue(self::$entries));
        $driver->expects($this->any())
            ->method('bind')
            ->will($this->returnValue(true));

        return $driver;
    }

    /**
     * Mock DI Container
     *
     * @return Container
     */
    private function getContainer()
    {
        $container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects($this->any())
            ->method('get')
            ->with($this->equalTo('doctrine'))
            ->will($this->returnValue($this->doctrine));

        return $container;
    }
}