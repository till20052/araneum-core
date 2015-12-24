<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Connection
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_connections")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ConnectionRepository")
 * @UniqueEntity(fields="name")
 * @package Araneum\Bundle\MainBundle\Entity
 */
class Connection
{
    use DateTrait;

    const CONN_POSTGRESS     = 1;
    const CONN_NGINX   = 2;
    const CONN_REDIS = 3;
    const CONN_RABBIT = 4;
    const CONN_TO_STR = 'Create';

    const STATUS_OK              = 0;
    const STATUS_SLOW            = 1;
    const STATUS_HAS_LOSS        = 2;
    const STATUS_HAS_NO_RESPONSE = 3;
    const STATUS_UNKNOWN_HOST    = 4;

    private static $statuses = [
        self::STATUS_OK => 'ok',
        self::STATUS_SLOW => 'slow',
        self::STATUS_HAS_LOSS => 'has_loss',
        self::STATUS_HAS_NO_RESPONSE => 'has_no_response',
        self::STATUS_UNKNOWN_HOST => 'unknown_host',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="smallint", name="type")
     */
    protected $type;

    /**
     * @ORM\Column(type="string", name="name", unique=true, length=35)
     * @Assert\Length(min=3, max=35)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="host", length=100)
     * @Assert\Length(min=3, max=100)
     */
    protected $host;

    /**
     * @ORM\Column(type="integer", name="port", length=8, nullable=true)
     * @Assert\Length(min=2, max=8)
     * @Assert\NotBlank()
     */
    protected $port;

    /**
     * @ORM\Column(type="string", name="user_name", length=100, nullable=true)
     * @Assert\Length(min=3, max=100)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^\w+$/")
     */
    protected $userName;

    /**
     * @ORM\Column(type="string", name="password", length=100, nullable=true)
     * @Assert\Length(min=6, max=100)
     * @Assert\NotBlank()
     */
    protected $password;

    /**
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="Runner", cascade={"detach", "persist"})
     * @ORM\JoinColumn(name="runner_id", referencedColumnName="id")
     */
    protected $runner;

    /**
     * Get list of Connection statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return self::$statuses;
    }

    /**
     * Get Connection status description
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Connection
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
     * Set name
     *
     * @param string $name
     * @return Connection
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
     * Set host
     *
     * @param string $host
     * @return Connection
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return Connection
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return Connection
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Connection
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Connection
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
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get Runners
     *
     * @return Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * Set Runner
     *
     * @param Runner $runner
     * @return Connection
     */
    public function setRunner(Runner $runner)
    {
        $this->runner = $runner;

        return $this;
    }

    /**
     * Convert entity to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ? $this->name." (".$this->host.")" : 'Create Connection';
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return Connection $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
