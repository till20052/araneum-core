<?php

namespace Araneum\Bundle\UserBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
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
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = [];
        foreach ($this->roles->toArray() as $k => $role) {

            $roles[$role->getId()] = $role->getName();
        }

        return $roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {

        //TODO  override
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    public function getRolesCollection()
    {
        return $this->roles->first();
    }

    /**
     * @param string $role
     * @return $this
     */
    public function removeRole($role)
    {
//        die(var_dump($role));

        //TODO исправь!!!
        $role = $this->roles->filter(

            function (Role $r) use ($role) {
                if ($role instanceof Role) {
                    return $r->getRole() === $role->getRole();
                } else {
                    return $r->getRole() === strtoupper($role);
                }
            }

        )->first();
        if ($role) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function addRole($role)
    {
        if (!$role instanceof Role) {
            $role = new Role($role);
        }

        $this->roles->add($role);

        return $this;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles()->toArray(), true);
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