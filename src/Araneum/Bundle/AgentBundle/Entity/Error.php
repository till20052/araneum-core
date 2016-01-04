<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Araneum\Bundle\MainBundle\Entity\Application;
use Symfony\Component\Validator\Constraints as Assert;
use Araneum\Base\EntityTrait\DateTrait;

/**
 * Error
 *
 * @ORM\Table("araneum_agent_errors")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\ErrorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Error
{
    use DateTrait;

    /**
     * Fatal error
     */
    const TYPE_FATAL = 1;

    /**
     * Warning
     */
    const TYPE_WARNING = 2;

    /**
     * Error types
     *
     * @var array
     */
    public static $types = [
        self::TYPE_FATAL => 'Fatal error',
        self::TYPE_WARNING => 'Warning',
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Application
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Application", inversedBy="errors")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    private $application;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint")
     * @Assert\Choice(choices = {1, 2})
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_at", type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $sentAt;

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
     * Set application
     *
     * @param  string $application
     * @return Error
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return string
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set type
     *
     * @param  integer $type
     * @return Error
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
     * Set message
     *
     * @param  string $message
     * @return Error
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set sentAt
     *
     * @param  \DateTime $sentAt
     * @return Error
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }
}
