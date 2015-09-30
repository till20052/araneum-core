<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToOne(targetEntity="Connection", inversedBy="cluster")
     * @ORM\JoinColumn(name="connection_id", referencedColumnName="id")
     */
    protected $host;

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
     * Set host
     *
     * @param Connection $host
     * @return $this
     */
    public function setHost(Connection $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return Connection
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * To string
     *
     * @return string $name
     **/
    public function __toString()
    {
        return 'Cluster'; //TODO необходимо подумать что выводить в этом методе
    }
}
