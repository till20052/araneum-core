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
     * The existence check full_name, email, username.
     *
     * @param array $ldapInfo
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function isLdapUser(array $ldapInfo)
    {

        return $this->createQueryBuilder('u')
            ->select('u.full_name, u.email, u.username')
            ->where('u.full_name = :full_name AND u.email = :email AND u.username = :username')
            ->setParameters([
                'full_name' => $ldapInfo['displayName'],
                'email'     => $ldapInfo['mail'],
                'username'  => $ldapInfo['uid'],
            ]);
    }
}
