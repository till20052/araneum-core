<?php
namespace Araneum\Bundle\MainBundle\Service;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Araneum\Bundle\MainBundle\Service\LdapService;
use Araneum\Bundle\UserBundle\Entity\User;
use Araneum\Bundle\UserBundle\Entity\UserLdapLog;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Service\Client;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LdapSynchronizationService
 *
 * @package Araneum\Bundle\MainBundle\Service
 */
class LdapSynchronizationService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LdapService
     */
    private $ldapService;

    /**
     * @var mixed
     */
    private $params;

    /**
     * Constructor
     *
     * @param EntityManager      $entityManager
     * @param LdapService        $ldapService
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $entityManager, LdapService $ldapService, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->ldapService = $ldapService;
        $this->params = $container->getParameter('ldap');
    }

    /**
     * ldap Synchronization from LDAP service
     * @return int
     * @throws \Exception
     */
    public function runSynchronization()
    {

        $this->ldapService->bind($this->params['base_dn'], $this->params['search_password']);
        $this->ldapService->setSearch($this->params['search_dn'], $this->params['filter'], "*");
        $entry = $this->ldapService->getFirstEntry();

        $result = [
            'sitem' => 0,
            'uitem' => 0,
        ];
        while ($entry) {
            $argument = $this->ldapService->getAttributes($entry);
            $arrItem = [];
            foreach ($this->ldapService->getLdapFields() as $field) {
                if (isset($argument[$field][0])) {
                    $arrItem[$field] = $argument[$field][0];
                }
            }
            if (count($arrItem) > 0 && $status = $this->createUser($arrItem)) {
                $result['sitem'] += ($status == UserLdapLog::STATUS_NEW)?1:0;
                $result['uitem'] += ($status == UserLdapLog::STATUS_UPDATE)?1:0;
            }

            $entry = $this->ldapService->getNextEntry();
        }
        $this->ldapService->disconnect();

        return $result;
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
        $repositoryUser = $this->entityManager->getRepository('AraneumUserBundle:User');
        $userByEmail = $repositoryUser->findOneByEmail($ldapInfo['mail']);

        if (empty($userByEmail)) {
            $user = new User();
            $user->setFullName($ldapInfo['displayName']);
            $user->setEmail($ldapInfo['mail']);
            $user->setEmailCanonical($ldapInfo['mail']);
            $user->setUsername($ldapInfo['uid']);
            $user->setUsernameCanonical($ldapInfo['uid']);
            $user->setRoles((is_array($this->params['user_role'])?$this->params['user_role']:[$this->params['user_role']]));
            $user->setEnabled(false);
            $user->setPlainPassword((isset($this->params['user_password']))?$this->params['user_password']:null);
            $this->entityManager->persist($user);
            $this->setUserLdapLog($user, $status = UserLdapLog::STATUS_NEW);
        } elseif (!empty($userByEmail->getId()) && !$repositoryUser->isHashLdapUser($ldapInfo)) {
            $user = new User($userByEmail->getId());
            $user->setFullName($ldapInfo['displayName']);
            $user->setEmail($ldapInfo['mail']);
            $user->setEmailCanonical($ldapInfo['mail']);
            $user->setUsername($ldapInfo['uid']);
            $user->setUsernameCanonical($ldapInfo['uid']);
            $this->entityManager->persist($user);
            $this->setUserLdapLog($user, $status = UserLdapLog::STATUS_UPDATE);
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Don't save user {$ldapInfo['uid']}. Error:".$e->getMessage());
        }

        return $status;
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
}
