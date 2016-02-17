<?php

namespace Araneum\Bundle\UserBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserLdapLog
 *
 * @ORM\Table(name="araneum_user_ldap_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\UserBundle\Repository\UserLdapLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserLdapLog
{
    const STATUS_NEW     = 1;
    const STATUS_UPDATE  = 2;

    use DateTrait;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return UserLdapLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param  User $user
     * @return UserLdapLog $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}
