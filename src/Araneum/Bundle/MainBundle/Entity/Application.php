<?php
namespace Araneum\Bundle\MainBundle\Entity;

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
    use Araneum\BaseBundle\EntityTrait\DateTrait;

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
     * @ORM\Column(type="json_array")
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
     * @ORM\ManyToMany(targetEntity="Component")
     * @ORM\JoinTable(name="araneum_components",
     *                  joinColumns={JoinColumn(name="component_id", referencedColumnName="id")},
     *                  inverseJoinColumns={JoinColumn(name="application_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $components;

    /**
     * @ORM\ManyToOne(targetEntity="User")
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * @param mixed $cluster
     * @return mixed
     */
    public function setCluster(Cluster $cluster)
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return mixed
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     * @return mixed
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @param mixed $aliases
     * @return mixed
     */
    public function setAliases(ArrayCollection $aliases)
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param mixed $db
     * @return mixed
     */
    public function setDb(Connection $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param mixed $public
     * @return mixed
     */
    public function setPublic($public = true)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     * @return mixed
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     * @return mixed
     */
    public function setLocale(Locale $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param mixed $components
     * @return mixed
     */
    public function setComponents(ArrayCollection $components)
    {
        $this->components = $components;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     * @return mixed
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return mixed
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     * @return mixed
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }
}