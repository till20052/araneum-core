<?php
namespace Araneum\Bundle\UserBundle\Repository;

use Araneum\Base\Repository\CountableTrait;
use Araneum\Base\Repository\AdminDataGridTrait;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @package Araneum\Bundle\UserBundle\Repository
 */
class UserRepository extends EntityRepository implements \Countable
{
    use CountableTrait;
    use AdminDataGridTrait;

    /**
     * Clear old user not used in LDAP services
     */
    public function clearOldLdapUsers()
    {
        $oldUsers = $this->createQueryBuilder('u')
            ->where('u.useLdap = true AND u.delLdap = true')
            ->getQuery()
            ->getResult();

        if (is_array($oldUsers)) {
            foreach ($oldUsers as $user) {
                $this->_em->remove($user);
                $this->_em->flush();
            }
        }
    }

    /**
     * Set add status old all users how used in LDAP
     */
    public function setAllLdapUsersStatusOld()
    {
        $users = $this->createQueryBuilder('u')
            ->where('u.useLdap = true AND u.delLdap = false')
            ->getQuery()
            ->getResult();

        if (is_array($users)) {
            foreach ($users as $user) {
                $user->setDelLdap(true);
                $this->_em->persist($user);
                $this->_em->flush();
            }
        }
    }

    /**
     * The existence check full_name, email, username.
     *
     * @param array $ldapInfo
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function isLdapUser(array $ldapInfo)
    {

        return $this->createQueryBuilder('u')
            ->select('u.fullName, u.email, u.username')
            ->where('u.fullName = :full_name AND u.username = :username AND u.email = :email AND u.useLdap = true')
            ->setParameters([
                'full_name' => $ldapInfo['displayname'],
                'email'     => $ldapInfo['mail'],
                'username'  => $ldapInfo['uid'],
            ])
            ->getQuery()
            ->getResult();
    }
}
