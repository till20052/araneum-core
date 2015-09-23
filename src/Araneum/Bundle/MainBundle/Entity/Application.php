<?php
namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Application class
 * @Doctrine\ORM\Mapping\Entity
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ApplicationRepository")
 * @ORM\Table(name="araneum_applications")
 *
 */
class Application
{
    use DateTrait;

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
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $domain;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $aliases;

    /**
     * @ORM\ManyToOne(targetEntity="Connection")
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
     * @ORM\ManyToOne(targetEntity="Locale")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id")
     */
    protected $locale;

    /**
     * @ORM\ManyToMany(targetEntity="Component", inversedBy="applications", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="araneum_component_application")
     */
    protected $components;

    /**
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $template;

    public function __construct()
    {
        $this->setComponents(new ArrayCollection());
        $this->setAliases(new ArrayCollection());
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
    public function setAliases(ArrayCollection $aliases)
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * Get Db
     *
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Set Db
     *
     * @param mixed $db
     * @return mixed
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
     * Get locale
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set locale
     *
     * @param mixed $locale
     * @return mixed
     */
    public function setLocale(Locale $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get Components
     *
     * @return mixed
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * Set components
     *
     * @param mixed $components
     * @return mixed
     */
    public function setComponents(ArrayCollection $components)
    {
        $this->components = $components;

        return $this;
    }

    /**
     * Get owner
     *
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set owner
     *
     * @param mixed $owner
     * @return mixed
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
}