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

    const SEND_OK = 1;
    const SEND_ERROR = 2;

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
     * @ORM\Column(name="mail_id", type="integer")
     */
    private $mail_id;

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
     * @param  integer $mail_id
     * @return MailLog
     */
    public function setMailId($mail_id)
    {
        $this->mail_id = $mail_id;

        return $this;
    }

    /**
     * Get mauil
     *
     * @return integer
     */
    public function getMailId()
    {
        return $this->mail_id;
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
