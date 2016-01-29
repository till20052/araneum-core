<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Runner;

/**
 * RunnerLog
 *
 * @ORM\Table(name="araneum_runner_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\RunnerLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RunnerLog
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
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var Cluster
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Cluster", inversedBy="clusterLogs")
     * @ORM\JoinColumn(name="cluster_id", referencedColumnName="id", nullable=false)
     */
    private $cluster;

    /**
     * @var Runner
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Runner", inversedBy="runnerLogs")
     * @ORM\JoinColumn(name="runner_id", referencedColumnName="id", nullable=false)
     */
    private $runner;

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
     * Set status
     *
     * @param  integer $status
     * @return ClusterLog
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
     * Set cluster
     *
     * @param  Cluster $cluster
     * @return ClusterLog $this
     */
    public function setCluster(Cluster $cluster)
    {
        $this->cluster = $cluster;

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
     * Set runner
     *
     * @param  Runner $runner
     * @return RunnerLog $this
     */
    public function setRunner(Runner $runner)
    {
        $this->runner = $runner;

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
}
