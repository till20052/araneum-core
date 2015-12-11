<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\AgentBundle\Entity\ConnectionLog;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Araneum\Bundle\AgentBundle\Entity\ClusterLog;

/**
 * Class Cluster
 *
 * @ORM\Table(name="araneum_cluster")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ClusterRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="name")
 * @package Araneum\Bundle\MainBundle\Entity
 */
class Cluster
{
    use DateTrait;

    const STATUS_OK                        = 0;
    const STATUS_HAS_INCORRECT_APPLICATION = 10;
    const STATUS_HAS_SLOW_CONNECTION       = 20;
    const STATUS_HAS_UNSTABLE_CONNECTION   = 25;
    const STATUS_OFFLINE                   = 30;

    private static $statuses = [
        self::STATUS_OK => 'ok',
        self::STATUS_HAS_INCORRECT_APPLICATION => 'has_incorrect_application',
        self::STATUS_HAS_SLOW_CONNECTION => 'has_slow_connection',
        self::STATUS_HAS_UNSTABLE_CONNECTION => 'has_unstable_connection',
        self::STATUS_OFFLINE => 'offline',
    ];

    const TYPE_SINGLE   = 1;
    const TYPE_MULTIPLE = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Assert\Type(type="int")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="name", unique=true, length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @Assert\Type(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Connection", inversedBy="cluster", cascade={"persist"})
     * @ORM\JoinTable(name="araneum_cluster_connection")
     */
    protected $hosts;

    /**
     * @ORM\Column(type="smallint", name="type", options={"comment": "1 - single, 2 - multiple"})
     * @Assert\Type(type="int")
     */
    protected $type = self::TYPE_MULTIPLE;

    /**
     * @ORM\Column(type="boolean", name="enabled")
     * @Assert\Type(type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="smallint", name="status")
     * @Assert\NotBlank()
     * @Assert\Type(type="int")
     */
    protected $status;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Application", mappedBy="cluster", cascade={"detach", "persist"})
     */
    protected $applications;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Araneum\Bundle\AgentBundle\Entity\ConnectionLog", mappedBy="cluster",
     *     cascade={"remove"})
     */
    protected $connectionLogs;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Araneum\Bundle\AgentBundle\Entity\ClusterLog", mappedBy="cluster",
     *     cascade={"remove"})
     */
    protected $clusterLogs;

    /**
     * Get list of Cluster statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return self::$statuses;
    }

    /**
     * Get Cluster status description
     *
     * @param integer $status
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
     * Cluster constructor.
     */
    public function __construct()
    {
        $this->setHosts(new ArrayCollection());
        $this->setApplications(new ArrayCollection());
        $this->setConnectionLogs(new ArrayCollection());
        $this->setClusterLogs(new ArrayCollection());
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
     * Set name
     *
     * @param string $name
     * @return Cluster
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Cluster
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Cluster
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = (bool) $enabled;

        return $this;
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
     * Set status
     *
     * @param integer $status
     * @return Cluster
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
     * Add host
     *
     * @param ArrayCollection $hosts
     * @return Cluster
     */
    public function setHosts(ArrayCollection $hosts)
    {
        $this->hosts = $hosts;

        return $this;
    }

    /**
     * Get host
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * Add single host in collection
     *
     * @param Connection $host
     * @return Cluster
     */
    public function addHost(Connection $host)
    {
        $this->getHosts()->add($host);

        return $this;
    }

    /**
     * Remove single host from collection
     *
     * @param Connection $host
     */
    public function removeHost(Connection $host)
    {
        $this->getHosts()->removeElement($host);
    }

    /**
     * Convert entity to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ?: 'Create Cluster';
    }

    /**
     * Set applications
     *
     * @param ArrayCollection $applications
     * @return Cluster $this
     */
    public function setApplications(ArrayCollection $applications)
    {
        $this->applications = $applications;

        return $this;
    }

    /**
     * Get applications
     *
     * @return ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Add application
     *
     * @param Application $application
     * @return Cluster $this
     */
    public function addApplication(Application $application)
    {
        if (!$this->hasApplication($application)) {
            $this->applications->add($application);
        }

        return $this;
    }

    /**
     * Remove application
     *
     * @param Application $application
     * @return Cluster $this
     */
    public function removeApplication(Application $application)
    {
        $this->applications->removeElement($application);

        return $this;
    }

    /**
     * Check is cluster has application
     *
     * @param Application $application
     * @return bool
     */
    public function hasApplication(Application $application)
    {
        return $this->applications->contains($application);
    }

    /**
     * Set connectionLog
     *
     * @param ArrayCollection $connectionLogs
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
     * @param ConnectionLog $connectionLogs
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
     * @param ConnectionLog $connectionLogs
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
     * @param ConnectionLog $connectionLogs
     * @return bool
     */
    public function hasConnectionLogs(ConnectionLog $connectionLogs)
    {
        return $this->connectionLogs->contains($connectionLogs);
    }

    /**
     * Set clusterLog
     *
     * @param ArrayCollection $clusterLogs
     * @return Cluster $this
     */
    public function setClusterLogs(ArrayCollection $clusterLogs)
    {
        $this->clusterLogs = $clusterLogs;

        return $this;
    }

    /**
     * Set clusterLogs
     *
     * @return ArrayCollection
     */
    public function getClusterLogs()
    {
        return $this->clusterLogs;
    }

    /**
     * Add clusterLog
     *
     * @param ClusterLog $clusterLogs
     * @return Cluster $this
     */
    public function addClusterLogs(ClusterLog $clusterLogs)
    {
        $this->clusterLogs->add($clusterLogs);

        return $this;
    }

    /**
     * Remove clusterLog
     *
     * @param ClusterLog $clusterLogs
     * @return Cluster $this
     */
    public function removeClusterLogs(ClusterLog $clusterLogs)
    {
        $this->clusterLogs->removeElement($clusterLogs);

        return $this;
    }

    /**
     * Check is cluster has connectionLogs
     *
     * @param ClusterLog $clusterLogs
     * @return bool
     */
    public function hasClusterLogs(ClusterLog $clusterLogs)
    {
        return $this->clusterLogs->contains($clusterLogs);
    }
}
