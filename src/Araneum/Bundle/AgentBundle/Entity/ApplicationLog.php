<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

/**
 * ApplicationLog
 *
 * @ORM\Table(name="araneum_applications_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\ApplicationLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ApplicationLog
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
     * @var Application
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Application", inversedBy="applicationLog")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    private $application;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(
     *      targetEntity="Araneum\Bundle\AgentBundle\Entity\Problem",
     *      cascade={"persist"}
     * )
     * @ORM\JoinTable(name="araneum_applications_log_problems")
     */
    private $problems;

    /**
     * ApplicationLog construct
     */
    public function __construct()
    {
        $this->problems = new ArrayCollection([]);
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
     * Set status
     *
     * @param  integer $status
     * @return ApplicationLog
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
     * Set application
     *
     * @param  Application $application
     * @return ApplicationLog $this
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set problems
     *
     * @param  Collection|ArrayCollection $problems
     * @return ApplicationLog $this
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
     * @param  Problem $problem
     * @return ApplicationLog $this
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
     * @param  Problem $problem
     * @return ApplicationLog $this
     */
    public function removeProblem(Problem $problem)
    {
        if (!$this->hasProblem($problem)) {
            $this->problems->remove($problem);
        }

        return $this;
    }

    /**
     * Has problem
     *
     * @param  Problem $problem
     * @return bool
     */
    public function hasProblem(Problem $problem)
    {
        return $this->problems->contains($problem);
    }
}
