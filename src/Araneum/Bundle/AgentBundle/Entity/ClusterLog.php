<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ClusterLog
 *
 * @ORM\Table(name="araneum_cluster_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\ClusterLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ClusterLog
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
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Cluster", inversedBy="clusterLogs",
     *     cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="cluster_id", referencedColumnName="id", nullable=false)
     */
    private $cluster;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(
     *      targetEntity="Araneum\Bundle\AgentBundle\Entity\Problem",
     *      cascade={"persist"}
     * )
     * @ORM\JoinTable(name="araneum_cluster_log_problems")
     */
    private $problems;

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
     * @param integer $status
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
     * @param Cluster $cluster
     * @return ClusterLog $this
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
     * Set problems
     *
     * @param Collection|ArrayCollection $problems
     * @return ClusterLog $this
     */
    public function setProblems(Collection $problems)
    {
        $this->problems = $problems;

        return $this;
    }

    /**
     * Get problems
     *
     * @return ArrayCollection
     */
    public function getProblems()
    {
        return $this->problems;
    }

    /**
     * Add problem
     *
     * @param Problem $problem
     * @return ClusterLog $this
     */
    public function addProblem(Problem $problem)
    {
        if (!$this->hasProblem($problem)) {
            $this->problems->add($problem);
        }

        return $this;
    }

    /**
     * Remove problem
     *
     * @param Problem $problem
     * @return ClusterLog $this
     */
    public function removeProblem(Problem $problem)
    {
        if ($this->hasProblem($problem)) {
            $this->problems->remove($problem);
        }

        return $this;
    }

    /**
     * Has problem
     *
     * @param Problem $problem
     * @return bool
     */
    public function hasProblem(Problem $problem)
    {
        return $this->problems->contains($problem);
    }
}
