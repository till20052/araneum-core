<?php

namespace Araneum\Bundle\MailBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Application;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mail
 *
 * @ORM\Table(name="araneum_mails")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MailBundle\Repository\MailRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Mail
{
    use DateTrait;

    const STATUS_NEW     = 1;
    const STATUS_PENDING = 2;
    const STATUS_SENT    = 3;
    const STATUS_READ    = 4;

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
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Application", inversedBy="mails")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    private $application;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="10", max = 255)
     * @ORM\Column(name="sender", type="string", length=255)
     */
    private $sender;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="10", max = 255)
     * @ORM\Column(name="target", type="string", length=255)
     */
    private $target;

    /**
     * @var string
     * @Assert\Length(max = 255)
     * @ORM\Column(name="headline", type="string", length=255, nullable=true)
     */
    private $headline;

    /**
     * @var string
     *
     * @ORM\Column(name="html_body", type="text", nullable=true)
     */
    private $htmlBody;

    /**
     * @var string
     *
     * @ORM\Column(name="text_body", type="text", nullable=true)
     */
    private $textBody;

    /**
     * @var string
     * @Assert\Length(max = 255)
     * @ORM\Column(name="attachment", type="string", length=255, nullable=true)
     */
    private $attachment;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * Mail constructor
     */
    function __construct()
    {
        $this->setStatus(self::STATUS_NEW);
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
     * Set application
     *
     * @param Application $application
     * @return Mail
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
     * Set sender
     *
     * @param string $sender
     * @return Mail
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return Mail
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set headline
     *
     * @param string $headline
     * @return Mail
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;

        return $this;
    }

    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * Set htmlBody
     *
     * @param string $htmlBody
     * @return Mail
     */
    public function setHtmlBody($htmlBody)
    {
        $this->htmlBody = $htmlBody;

        return $this;
    }

    /**
     * Get htmlBody
     *
     * @return string
     */
    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    /**
     * Set testBody
     *
     * @param string $textBody
     * @return Mail
     */
    public function setTextBody($textBody)
    {
        $this->textBody = $textBody;

        return $this;
    }

    /**
     * Get testBody
     *
     * @return string
     */
    public function getTextBody()
    {
        return $this->textBody;
    }

    /**
     * Set attachment
     *
     * @param string $attachment
     * @return Mail
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * Get attachment
     *
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Mail
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
     * Set sentAt
     *
     * @param \DateTime $sentAt
     * @return Mail
     */
    public function setSentAt(\DateTime $sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return string
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Mark Mail as sent
     */
    public function markAsSent()
    {
        $this->setSender(new \DateTime());
        $this->setStatus(self::STATUS_SENT);
    }

    /**
     * Convert entity to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->id . ' ' . $this->headline : 'Create Mail';
    }

}