<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Cluster
 * @ORM\Table(name="araneum_cluster")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ClusterRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="name")
 * @package Araneum\Bundle\MainBundle\Entity
 */
class Cluster
{
    use DateTrait;

    const STATUS_ONLINE = 1;
    const STATUS_OFFLINE = 2;

    const TYPE_SINGLE = 1;
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
     * @ORM\ManyToMany(targetEntity="Connection", inversedBy="cluster", cascade={"detach"})
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
     * @Assert\Type(type="bool")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="smallint", name="status", options={"comment":"1 - online, 2 - offline"})
     * @Assert\NotBlank()
     * @Assert\Type(type="int")
     */
    protected $status;

    /**
     * Cluster constructor.
     */
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
     * Remove single host
     *
     * @param Connection $host
     * @return Cluster
     */
    public function removeHost(Connection $host)
    {
        $this->getHosts()->removeElement($host);

        return $this;
    }
}
