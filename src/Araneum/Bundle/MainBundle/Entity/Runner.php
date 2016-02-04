<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Predis\Command\ConnectionEcho;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Araneum\Bundle\AgentBundle\Entity\ConnectionLog;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Connection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Runner
 *
 * @ORM\Table(name="araneum_runner")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\RunnerRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="name")
 *
 * @package Araneum\Bundle\MainBundle\Entity
 */
class Runner
{
    use DateTrait;

    const STATUS_OK                        = 0;
    const STATUS_CODE_INCORRECT            = 1;
    const STATUS_ERROR                     = 2;
    const STATUS_APP_CODE_INCORRECT        = 10;
    const STATUS_APP_ERROR                 = 15;
    const STATUS_HAS_SLOW_CONNECTION       = 20;
    const STATUS_HAS_UNSTABLE_CONNECTION   = 25;
    const STATUS_OFFLINE                   = 30;

    private static $statuses = [
        self::STATUS_OK => 'ok',
        self::STATUS_CODE_INCORRECT => 'status_code_incorrect',
        self::STATUS_ERROR => 'error',
        self::STATUS_APP_CODE_INCORRECT => 'application_status_code_incorrect',
        self::STATUS_APP_ERROR => 'error_application',
        self::STATUS_HAS_SLOW_CONNECTION => 'has_slow_connection',
        self::STATUS_HAS_UNSTABLE_CONNECTION => 'has_unstable_connection',
        self::STATUS_OFFLINE => 'offline',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Assert\Type(type="int")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="name", unique=true, length=35)
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=35)
     * @Assert\Type(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\Regex("/^((?!-)[A-Za-z0-9-]{1,63}(?<!-)\.)+[A-Za-z]{2,6}$/", message="domain_not_valid_url")
     */
    protected $domain;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Connection", mappedBy="runner", cascade={"detach", "persist"})
     */
    protected $connections;

    /**
     * @ORM\Column(type="boolean", name="enabled")
     * @Assert\Type(type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="smallint", name="type")
     * @Assert\Type(type="int")
     */
    protected $type;

    /**
     * @ORM\Column(type="smallint", name="status")
     * @Assert\NotBlank()
     * @Assert\Type(type="int")
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="Cluster", cascade={"detach", "persist"})
     * @ORM\JoinColumn(name="cluster_id", referencedColumnName="id")
     */
    protected $cluster;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Araneum\Bundle\AgentBundle\Entity\ConnectionLog", mappedBy="runner",
     *                cascade={"persist", "remove"})
     */
    protected $connectionLogs;

    /**
     * @ORM\Column(type="boolean", name="use_ssl")
     * @Assert\Type(type="boolean")
     */
    protected $useSSL;

    /**
     * Runner constructor.
     */
    public function __construct()
    {
        $this->setConnections(new ArrayCollection());
        $this->setConnectionLogs(new ArrayCollection());
    }

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
     * Set id
     *
     * @param  mixed $id
     * @return Runner
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param  mixed $name
     * @return Runner
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domain
     *
     * @param  mixed $domain
     * @return Runner
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get connections
     *
     * @return ArrayCollection
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Set connections
     *
     * @param  mixed $connections
     * @return Runner
     */
    public function setConnections($connections)
    {
        $this->connections = $connections;

        return $this;
    }

    /**
     * Add connection
     *
     * @param  Connection $connection
     * @return Cluster
     */
    public function addConnection(Connection $connection)
    {
        $this->getConnections()->add($connection);

        return $this;
    }

    /**
     * Remove single connection
     *
     * @param Connection $connection
     */
    public function removeHost(Connection $connection)
    {
        $this->getConnections()->removeElement($connection);
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param  boolean $enabled
     * @return Runner
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }

    /**
     * Set type
     *
     * @param  integer $type
     * @return Runner
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return Runner
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
     * Get Runner status description
     *
     * @param  integer $status
     * @return string
     */
    public static function getStatusDescription($status)
    {
        if (!isset(self::$statuses[$status])) {
            return '[undefined]';
        }

        return self::$statuses[$status];
    }

    /**
     * Get cluster
     *
     * @return Cluster
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * Set cluster
     *
     * @param  Cluster $cluster
     * @return $this
     */
    public function setCluster(Cluster $cluster)
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * Set connectionLog
     *
     * @param  ArrayCollection $connectionLogs
     * @return Cluster $this
     */
    public function setConnectionLogs(ArrayCollection $connectionLogs)
    {
        $this->connectionLogs = $connectionLogs;

        return $this;
    }

    /**
     * Set connectionsLog
     *
     * @return ArrayCollection
     */
    public function getConnectionLogs()
    {
        return $this->connectionLogs;
    }

    /**
     * Add connectionsLog
     *
     * @param  ConnectionLog $connectionLogs
     * @return Cluster $this
     */
    public function addConnectionLogs(ConnectionLog $connectionLogs)
    {
        $this->connectionLogs->add($connectionLogs);

        return $this;
    }

    /**
     * Remove connectionsLog
     *
     * @param  ConnectionLog $connectionLogs
     * @return Cluster $this
     */
    public function removeConnectionLogs(ConnectionLog $connectionLogs)
    {
        $this->connectionLogs->removeElement($connectionLogs);

        return $this;
    }

    /**
     * Check is cluster has connectionLogs
     *
     * @param  ConnectionLog $connectionLogs
     * @return bool
     */
    public function hasConnectionLogs(ConnectionLog $connectionLogs)
    {
        return $this->connectionLogs->contains($connectionLogs);
    }

    /**
     * Get useSSL
     *
     * @return boolean
     */
    public function isUseSSL()
    {
        return (bool) $this->useSSL;
    }

    /**
     * Set useSSL
     *
     * @param  boolean $useSSL
     * @return Runner
     */
    public function setUseSSL($useSSL = true)
    {
        $this->useSSL = (bool) $useSSL;

        return $this;
    }
}
