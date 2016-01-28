<?php

namespace Araneum\Bundle\UserBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreFlushEventArgs;
use \FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User class
 *
 * @ORM\Entity(repositoryClass="Araneum\Bundle\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_users")
 * @ORM\AttributeOverrides({
 *  @ORM\AttributeOverride(
 *      name="username",
 *      column=@ORM\Column(name="username", type="string", length=35)
 *  ),
 *  @ORM\AttributeOverride(
 *      name="usernameCanonical",
 *      column=@ORM\Column(name="username_canonical", type="string", length=35, nullable=true)
 *  )
 * })
 * @UniqueEntity(
 *     fields="email",
 *     message="email.already_used"
 * )
 */
class User extends BaseUser
{
    use DateTrait;

    const ROLE_USER  = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_API   = 'ROLE_API';

    /**
     * @var array
     */
    public static $roleNames = [
        'ROLE_USER' => self::ROLE_USER,
        'ROLE_ADMIN' => self::ROLE_ADMIN,
        'ROLE_API' => self::ROLE_API,
    ];

    /**
     * @var array $enable
     */
    public static $enable = [
        true => 'Enabled',
        false => 'Disabled',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Araneum\Bundle\UserBundle\Entity\Role", cascade={"detach", "persist"},
     *                                                                       inversedBy="users")
     * @ORM\JoinTable(name="araneum_user_role")
     */
    protected $roles;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string", name="full_name", nullable=true, length=35)
     * @Assert\NotBlank()
     */
    private $fullName;

    /**
     * @var array
     */
    private $rolesBuffer;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $settings;

    /**
     * @var string
     * @Assert\Length(min="2", max="255")
     * @Assert\Email()
     */
    protected $email;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSettings([]);
        $this->roles = new ArrayCollection();
    }

    /**
     * After load data
     *
     * @ORM\PostLoad
     */
    public function afterLoad()
    {
        $this->rolesBuffer = [];

        if (is_array($this->roles) || $this->roles instanceof \Traversable) {
            foreach ($this->roles as $role) {
                $this->rolesBuffer[] = $role->getName();
            }
        }
    }

    /**
     * Pre Flush Event
     *
     * @ORM\PreFlush
     * @param        PreFlushEventArgs $preFlushEventArgs
     */
    public function beforeFlush(PreFlushEventArgs $preFlushEventArgs)
    {
        $roleRepository = $preFlushEventArgs
            ->getEntityManager()
            ->getRepository('AraneumUserBundle:Role');

        $this->roles->clear();

        foreach ($this->getRoles() as $roleName) {
            $this->roles->add($roleRepository->findOneByName($roleName));
        }
    }

    /**
     * Get user roles
     *
     * @return array
     */
    public function getRoles()
    {
        if (isset($this->rolesBuffer)
            && is_array($this->rolesBuffer)
        ) {
            return $this->rolesBuffer;
        }

        $this->rolesBuffer = [];

        foreach ($this->roles as $role) {
            $this->rolesBuffer[] = $role->getName();
        }

        return $this->rolesBuffer;
    }

    /**
     * Set user roles
     *
     * @param  array $roles
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->rolesBuffer = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Set user role
     *
     * @param  string $role
     * @return User
     */
    public function setRole($role)
    {
        $this->setRoles([$role]);

        return $this;
    }

    /**
     * Get user role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->roles->first();
    }

    /**
     * Add user role
     *
     * @param  string $role
     * @return $this
     */
    public function addRole($role)
    {
        $role = strtoupper($role);

        if (!in_array($role, $this->getRoles(), true)) {
            $this->rolesBuffer[] = $role;
        }

        return $this;
    }

    /**
     * get user roles collection
     *
     * @return Collection
     */
    public function getRolesCollection()
    {
        return $this->roles;
    }

    /**
     * Remove user role
     *
     * @param  string $role
     * @return $this
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->getRoles(), true)) {
            unset($this->rolesBuffer[$key]);
            $this->rolesBuffer = array_values($this->rolesBuffer);
        }

        return $this;
    }

    /**
     * Check has user role
     *
     * @param  string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
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
     * @param  string $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get settings
     *
     * @return string
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set settings
     *
     * @param  array $settings
     * @return $this
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;

        return $this;
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
