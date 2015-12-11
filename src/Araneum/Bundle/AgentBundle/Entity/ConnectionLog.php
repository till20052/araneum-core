<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConnectionLog
 *
 * @ORM\Table(name="araneum_connection_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\ConnectionLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ConnectionLog
{
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
     * @var Connection
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Connection")
     * @ORM\JoinColumn(name="connection_id", referencedColumnName="id")
     */
    private $connection;

    /**
     * @var Cluster
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Cluster", inversedBy="connectionLogs",
     *     cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="cluster_id", referencedColumnName="id", nullable=false)
     */
    private $cluster;

    /**
     * @var integer
     *
     * @ORM\Column(name="percent_lost_packages", type="integer")
     */
    private $percentLostPackages;

    /**
     * @var integer
     *
     * @ORM\Column(name="average_ping_time", type="float")
     */
    private $averagePingTime;

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
     * Set connection
     *
     * @param Connection $connection
     * @return ConnectionLog $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Get connection
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set cluster
     *
     * @param Cluster $cluster
     * @return ConnectionLog $this
     */
    public function setCluster(Cluster $cluster)
    {
        $this->cluster = $cluster;

        return $this;
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
     * Set percentLostPackages
     *
     * @param integer $percentLostPackages
     * @return ConnectionLog
     */
    public function setPercentLostPackages($percentLostPackages)
    {
        $this->percentLostPackages = $percentLostPackages;

        return $this;
    }

    /**
     * Get percentLostPackages
     *
     * @return integer
     */
    public function getPercentLostPackages()
    {
        return $this->percentLostPackages;
    }

    /**
     * Set averagePingTime
     *
     * @param integer $averagePingTime
     * @return ConnectionLog
     */
    public function setAveragePingTime($averagePingTime)
    {
        $this->averagePingTime = $averagePingTime;

        return $this;
    }

    /**
     * Get averagePingTime
     *
     * @return integer
     */
    public function getAveragePingTime()
    {
        return $this->averagePingTime;
    }
}
