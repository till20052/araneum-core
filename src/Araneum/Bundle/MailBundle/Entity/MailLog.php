<?php

namespace Araneum\Bundle\MailBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Araneum\Bundle\MailBundle\Entity\Mail;
use Doctrine\ORM\Mapping as ORM;

/**
 * MailLog
 *
 * @ORM\Table(name="araneum_mails_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MailBundle\Repository\MailLogRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MailLog
{
    use DateTrait;

    const STATUS_OK       = 1;
    const STATUS_ERROR    = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Mail
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MailBundle\Entity\Mail", inversedBy="mails")
     * @ORM\JoinColumn(name="mail_id", referencedColumnName="id")
     */
    private $mail;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

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
     * Set mail
     *
     * @param  Mail $mail
     * @return MailLog
     */
    public function setMail(Mail $mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return MailLog
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
}
