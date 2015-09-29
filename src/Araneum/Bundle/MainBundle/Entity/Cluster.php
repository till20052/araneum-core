<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Cluster
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_cluster")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ClusterRepository")
 * @package Araneum\Bundle\MainBundle\Entity
 */
class Cluster
{
    use DateTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="name", unique=true, length=100)
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Connection", inversedBy="cluster", cascade={"detach"})
     * @ORM\JoinTable(name="araneum_cluster_connection")
     */
    protected $hosts;

    /**
     * @ORM\Column(type="smallint", name="type", options={"comment":"1 - single, 2 - multiple"})
     *
     */
    protected $type = 2;

    /**
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="smallint", name="status", options={"comment":"1 - online, 2 - offline"})
     */
    protected $status;

    public function __construct()
    {
        $this->setHosts(new ArrayCollection());
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
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
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
}
