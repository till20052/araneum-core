<?php

namespace Araneum\Bundle\UserBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\ORM\Event\PreFlushEventArgs;
use \FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User class
 *
 * @ORM\Entity(repositoryClass="Araneum\Bundle\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_users")
 *
 */
class User extends BaseUser
{
    use DateTrait;

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_API = 'ROLE_API';

    /**
     * @var array
     */
    public static $roleNames = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
        self::ROLE_API
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="full_name", nullable=true)
     */
    private $fullName;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Role", cascade={"detach", "persist"}, inversedBy="users")
     * @ORM\JoinTable(name="araneum_user_role")
     */
    protected $roles;

    /**
     * @var array
     */
    private $rolesBuffer;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = new ArrayCollection();
    }

    /**
     * Pre Flush Event
     *
     * @param PreFlushEventArgs $preFlushEventArgs
     * @ORM\PreFlush
     */
    public function beforeFlush(PreFlushEventArgs $preFlushEventArgs)
    {
        $roleRepository = $preFlushEventArgs
            ->getEntityManager()
            ->getRepository('AraneumUserBundle:Role');

        $this->roles->clear();

        foreach($this->getRoles() as $roleName)
        {
            $this->roles->add($roleRepository->findOneByName($roleName));
        }
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get user roles
     *
     * @return array
     */
    public function getRoles()
    {
        if(isset($this->rolesBuffer) && is_array($this->rolesBuffer)){
            return $this->rolesBuffer;
        }

        $this->rolesBuffer = [];

        foreach($this->roles as $role)
        {
            $this->rolesBuffer[] = $role->getName();
        }

        return $this->rolesBuffer;
    }

    /**
     * Set user roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles)
    {
        foreach($roles as $role)
        {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Add user role
     *
     * @param string $role
     * @return $this
     */
    public function addRole($role)
    {
        if( ! $this->hasRole($role)){
            $this->rolesBuffer[] = $role;
        }

        return $this;
    }

    /**
     * Remove user role
     *
     * @param string $roleName
     * @return $this
     */
    public function removeRole($roleName)
    {
        foreach($this->getRoles() as $i => $name)
        {
            if($roleName != $name){
                continue;
            }

            unset($this->rolesBuffer[$i]);
        }

        return $this;
    }

    /**
     * Check has user role
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        foreach($this->getRoles() as $name)
        {
            if($roleName != $name)
                continue;

            return true;
        }

        return false;
    }

    /**
     * Convert entity to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->username ?: 'Create User';
    }
}