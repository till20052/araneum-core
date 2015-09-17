<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Cluster
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_cluster")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ClusterRepository")
 * @package Araneum\Bundle\MainBundle\Entity
 */
class Cluster
{
    /**
     *
     */
    public function __construct()
    {
        $this->host = new ArrayCollection();
    }

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
     * @ORM\OneToMany(targetEntity="Connection", mappedBy="id", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="connection_id", referencedColumnName="id")
     */
    protected $host;

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
     * @ORM\Column(type="smallint", name="status", options={"comment":"1-online, 2-offline"})
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updated_at;

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
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Cluster
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Cluster
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Add host
     *
     * @param \Araneum\Bundle\MainBundle\Entity\Connection $host
     * @return Cluster
     */
    public function addHost(\Araneum\Bundle\MainBundle\Entity\Connection $host)
    {
        $this->host[] = $host;

        return $this;
    }

    /**
     * Remove host
     *
     * @param \Araneum\Bundle\MainBundle\Entity\Connection $host
     */
    public function removeHost(\Araneum\Bundle\MainBundle\Entity\Connection $host)
    {
        $this->host->removeElement($host);
    }

    /**
     * Get host
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHost()
    {
        return $this->host;
    }
}
