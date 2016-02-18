<?php
namespace Araneum\Bundle\MainBundle\Service;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use FOS\UserBundle\Model\UserManager;
use Araneum\Bundle\MainBundle\Service\LdapService;
use Araneum\Bundle\UserBundle\Entity\User;
use Araneum\Bundle\UserBundle\Entity\UserLdapLog;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use FR3D\LdapBundle\Driver\LdapDriverInterface;
use FR3D\LdapBundle\Ldap\LdapManager;

/**
 * Class LdapSynchronizationService
 *
 * @package Araneum\Bundle\MainBundle\Service
 */
class LdapSynchronizationService extends LdapManager
{
    /**
     * @var \Araneum\Bundle\UserBundle\Repository\UserRepository
     */
    private $repositoryUser;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|\Doctrine\ORM\EntityManager|object
     */
    private $entityManager;

    /**
     * @var mixed
     */
    private $ldapParameter;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * LdapSynchronizationService constructor.
     *
     * @param ContainerInterface      $container
     * @param EncoderFactoryInterface $encoderFactory
     * @param LdapDriverInterface     $driver
     * @param object                  $userManager
     * @param array                   $params
     */
    public function __construct(
        ContainerInterface $container,
        EncoderFactoryInterface $encoderFactory,
        LdapDriverInterface $driver,
        $userManager,
        array $params
    ) {
        parent::__construct($driver, $userManager, $params);
        $this->container = $container;
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->ldapParameter = $this->container->getParameter('ldap');
        $this->repositoryUser = $this->entityManager->getRepository('AraneumUserBundle:User');
    }

    /**
     * Add new query for LDAP filters
     * @param array $filters
     * @return $this
     */
    public function setFilterQuery(array $filters)
    {
        $this->params['filter'] = $this->buildFilter($filters);

        return $this;
    }

    /**
     * Change params API.LDAP
     * @param array $params
     * @return $this
     */
    public function setLdapParameter(array $params)
    {
        $this->ldapParameter = $params;

        return $this;
    }

    /**
     * ldap Synchronization from LDAP service
     * @return int
     * @throws \Exception
     */
    public function runSynchronization()
    {
        $result = [
            'sitem' => 0,
            'uitem' => 0,
        ];
        $this->repositoryUser->setAllLdapUsersStatusOld();
        $entries = $this->driver->search($this->params['baseDn'], $this->params['filter'], $this->ldapAttributes);
        if (is_array($entries)) {
            foreach ($entries as $entry) {
                if (is_array($entry) && $status = $this->createUser($this->generateDataParam($entry))) {
                    switch ($status) {
                        case UserLdapLog::STATUS_NEW:
                            $result['sitem'] += 1;
                            break;
                        case UserLdapLog::STATUS_UPDATE:
                            $result['uitem'] += 1;
                            break;
                    }
                }
            }
        }
        $this->repositoryUser->clearOldLdapUsers();

        return $result;
    }

    /**
     * Parce results from LDAP service
     * @param array $data
     * @return array
     */
    public function generateDataParam(array $data)
    {
        $res = [];
        foreach ($data as $key => $item) {
            $res[$key] = (is_array($item)) ? array_shift($item) : $item;
        }

        return $res;
    }

    /**
     * User LDAP log
     * @param User    $user
     * @param integer $status
     */
    public function setUserLdapLog(User $user, $status = UserLdapLog::STATUS_NEW)
    {
        $userLdapLog = new UserLdapLog();
        $userLdapLog->setUser($user);
        $userLdapLog->setStatus($status);
        $this->entityManager->persist($userLdapLog);
        $this->entityManager->flush();
    }


    /**
     * Created User and create user ldap log
     * @param array $ldapInfo
     * @return bool|void
     * @throws \Exception
     */
    private function createUser(array $ldapInfo)
    {
        if (!isset($ldapInfo['mail'])) {

            return;
        }

        $status = null;
        $userByEmail = $this->repositoryUser->findOneByEmail($ldapInfo['mail']);

        if (empty($userByEmail)) {
            $user = new User();
            $user->setFullName($ldapInfo['displayname']);
            $user->setEmail($ldapInfo['mail']);
            $user->setEmailCanonical($ldapInfo['mail']);
            $user->setUsername($ldapInfo['uid']);
            $user->setUsernameCanonical($ldapInfo['uid']);
            if (is_string($this->ldapParameter['default_user_roles'])) {
                $user->setRole($this->ldapParameter['default_user_roles']);
            } elseif (is_array($this->ldapParameter['default_user_roles'])) {
                $user->setRoles($this->ldapParameter['default_user_roles']);
            }
            $user->setPassword('');
            if (isset($ldapInfo['krblastpwdchange'])) {
                $user->setLastChangeLdapPass($ldapInfo['krblastpwdchange']);
            }
            $user->setEnabled(true);
            $user->setUseLdap(true);
            $user->setDelLdap(false);
            $this->entityManager->persist($user);
            $this->setUserLdapLog($user, $status = UserLdapLog::STATUS_NEW);
        } elseif (!empty($userByEmail->getId())) {
            if (!$this->repositoryUser->isLdapUser($ldapInfo)) {
                $userByEmail->setFullName($ldapInfo['displayname']);
                $userByEmail->setEmail($ldapInfo['mail']);
                $userByEmail->setEmailCanonical($ldapInfo['mail']);
                $userByEmail->setUsername($ldapInfo['uid']);
                $userByEmail->setUsernameCanonical($ldapInfo['uid']);
                $this->setUserLdapLog($userByEmail, $status = UserLdapLog::STATUS_UPDATE);
            }

            if (isset($ldapInfo['krblastpwdchange'])
                && $userByEmail->getPassword() != null
                && $userByEmail->getLastChangeLdapPass() != null
                && $userByEmail->getLastChangeLdapPass() != new \DateTime($ldapInfo['krblastpwdchange'])) {
                $userByEmail->setPassword('');
            }

            $userByEmail->setDelLdap(false);
            $this->entityManager->persist($userByEmail);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Don't save user {$ldapInfo['uid']}. Error:".$e->getMessage());
        }

        return $status;
    }
}
