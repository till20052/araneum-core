<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Application class.
 *
 * @Doctrine\ORM\Mapping\Entity
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ApplicationRepository")
 * @ORM\Table(name="araneum_applications")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="name")
 */
class Application
{
    use DateTrait;

    const STATUS_UNDEFINED = '';
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cluster")
     * @ORM\JoinColumn(name="cluster_id", referencedColumnName="id")
     */
    protected $cluster;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\Regex("/^((?!-)[A-Za-z0-9-]{1,63}(?<!-)\.)+[A-Za-z]{2,6}$/", message="application_domain_not_valid_url")
     */
    protected $domain;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $aliases;

    /**
     * @ORM\ManyToOne(targetEntity="Connection", cascade={"persist", "detach"})
     * @ORM\JoinColumn(name="connection_id", referencedColumnName="id")
     */
    protected $db;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $public;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Locale", inversedBy="applications", cascade={"persist"})
     * @ORM\JoinTable(name="araneum_applications_locales")
     */
    protected $locales;

    /**
     * @ORM\ManyToMany(targetEntity="Component", inversedBy="applications", cascade={"persist"})
     * @ORM\JoinTable(name="araneum_component_application")
     */
    protected $components;

    /**
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     */
    protected $template;

    /**
     * @ORM\Column(type="string", name="api_key", length=70)
     */
    protected $apiKey;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->setComponents(new ArrayCollection());
        $this->setApiKey();
    }

    /**
     * Get id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param mixed $id
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get cluster
     *
     * @return mixed
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * Set cluster
     *
     * @param mixed $cluster
     * @return mixed
     */
    public function setCluster(Cluster $cluster)
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param mixed $name
     * @return mixed
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get domain
     *
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domain
     *
     * @param mixed $domain
     * @return mixed
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get aliases
     *
     * @return mixed
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set aliases
     *
     * @param mixed $aliases
     * @return mixed
     */
    public function setAliases($aliases)
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * Get Db
     *
     * @return Connection
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Set Db
     *
     * @param Connection $db
     * @return $this
     */
    public function setDb(Connection $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Get public
     *
     * @return mixed
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * Set public
     *
     * @param mixed $public
     * @return mixed
     */
    public function setPublic($public = true)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param mixed $enabled
     * @return mixed
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get locales
     *
     * @return ArrayCollection
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Set locales
     *
     * @param ArrayCollection $locales
     * @return $this
     */
    public function setLocales(ArrayCollection $locales)
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * Get Components
     *
     * @return ArrayCollection
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * Set components
     *
     * @param ArrayCollection $components
     * @return $this
     */
    public function setComponents(ArrayCollection $components)
    {
        $this->components = $components;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set owner
     *
     * @param User $owner
     * @return $this
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param mixed $status
     * @return mixed
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get template
     *
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set template
     *
     * @param mixed $template
     * @return mixed
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set apiKey
     *
     * @param mixed $apiKey
     * @return mixed
     */
    public function setApiKey($apiKey = null)
    {
        $this->apiKey = is_null($apiKey) ? $this->generateUniqueKey() : $apiKey;

        return $this;
    }

    /**
     * Generate unique key for Application
     *
     * @return string
     */
    private function generateUniqueKey()
    {
        return uniqid(sha1(time()), true);
    }

    /**
     * Convert entity to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ?: 'Create Application';
    }
}