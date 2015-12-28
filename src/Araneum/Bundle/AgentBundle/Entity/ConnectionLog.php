<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Runner;
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
     * @var Runner
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Runner", inversedBy="connectionLogs",
     *     cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="runner_id", referencedColumnName="id", nullable=true)
     */
    private $runner;

    /**
     * @var integer
     * @ORM\Column(name="percent_lost_packages", type="integer")
     */
    private $percentLostPackages;

    /**
     * @var integer
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
     * @param  Connection $connection
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
     * Set runner
     *
     * @param  Runner $runner
     * @return ConnectionLog $this
     */
    public function setRunner(Runner $runner)
    {
        $this->runner = $runner;

        return $this;
    }

    /**
     * Get runner
     *
     * @return Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * Set percentLostPackages
     *
     * @param  integer $percentLostPackages
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
     * @param  integer $averagePingTime
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
