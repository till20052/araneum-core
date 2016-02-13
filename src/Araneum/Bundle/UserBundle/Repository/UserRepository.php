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

    public function isHashLdapUser(array $ldapInfo) {

        return $this->createQueryBuilder('REPO')
            ->select('u.full_name,u.email,u.username')
            ->from('AraneumUserBundle:User','u')
            ->where('u.full_name=:full_name AND u.email=:email AND u.username=:username')
            ->setParameters([
                'full_name' => $ldapInfo['displayName'],
                'email'     => $ldapInfo['mail'],
                'username'  => $ldapInfo['uid'],
            ]);
    }
}
