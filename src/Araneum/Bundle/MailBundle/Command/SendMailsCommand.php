<?php
namespace Araneum\Bundle\MailBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Araneum\Bundle\MailBundle\Entity\Mail;
use Araneum\Bundle\MailBundle\Entity\MailLog;

/**
 * Class SendMailsCommand
 * @package Araneum\Bundle\MailBundle\Command
 */
class SendMailsCommand extends ContainerAwareCommand
{
    /**
     * Set content type
     * @var array
     */
    protected static $contentTypes = [
        'html' => 'text/html',
        'plain' => 'text/plain',
    ];

    /**
     * Set charset to sendmailer
     * @type string
     */
    const CHARSET = 'UTF-8';

    /**
     * Set Default limit send mails
     * @type integer
     */
    const DEFAULTLIMIT = 10;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var FormatterHelper
     */
    private $formatter;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('araneum:send:emails')
            ->setDescription('Send all mails from queue')
            ->addOption(
                'limit',
                '-l',
                InputOption::VALUE_REQUIRED,
                'Limit of mails send each time',
                self::DEFAULTLIMIT
            );
    }

    /**
     * Execute command
     *
     * @inheritdoc
     * @param      InputInterface  $input
     * @param      OutputInterface $output
     *
     * @return null;
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->fs = new Filesystem();
        $limit = $input->getOption('limit');
        $this->sendAllMail($limit);
    }

    /**
     * Method send all mails messange.
     *
     * @param $limit
     * @throws \Exception
     */
    private function sendAllMail($limit)
    {
        $messangeCount = 0;
        $manager = $this->getContainer()
            ->get('doctrine')
            ->getManager();
        $repository = $manager->getRepository("AraneumMailBundle:Mail");
        $mails = $repository->getAllEmails($limit);
        if (is_array($mails) && count($mails)) {
            foreach ($mails as $mail) {
                $message = \Swift_Message::newInstance()
                    ->setSubject($mail->getHeadline())
                    ->setFrom($mail->getSender())
                    ->setTo($mail->getTarget())
                    ->setCharset(self::CHARSET)
                    ->setContentType(self::$contentTypes['plain'])
                    ->setBody($mail->getHtmlBody(), self::$contentTypes['html'])
                    ->addPart($mail->getTextBody(), self::$contentTypes['plain']);
                if (!empty($mail->getAttachment()) && $this->fs->exists($mail->getAttachment())) {
                    $message->attach(\Swift_Attachment::fromPath($mail->getAttachment()));
                }
                if ($msg = $this->getContainer()->get('mailer')->send($message)) {
                    try {
                        $eMail = $mail->setStatus(Mail::STATUS_SENT);
                        $manager->persist($eMail);
                        $manager->flush();
                        $messangeCount++;
                        $this->setLog($mail, MailLog::STATUS_OK);
                    } catch (\Exception $e) {
                        throw new \Exception("Don't save email status. Error:".$e->getMessage());
                    }
                } else {
                    $this->setLog($mail, MailLog::STATUS_ERROR);
                    $this->showError("Error! Not send messange to ".$mail->getId().':'.$msg);
                }
            }
            $this->output->writeln("Messange $messangeCount send to mail");
        } else {
            $this->showError("There is not data to send.");
        }
    }

    /**
     * Set log sender mails
     *
     * @param Mail $mail
     * @param int $status
     * @return boolean
     */
    private function setLog(Mail $mail, $status = MailLog::STATUS_OK)
    {
        $manager = $this->getContainer()
            ->get('doctrine')
            ->getManager();
        $manager->getRepository("AraneumMailBundle:MailLog")
                ->setMailLog($mail, $status);

        return true;
    }

    /**
     * Get Formatted Notification Block
     *
     * @param             $messages
     * @param             $style
     * @param  bool|false $large
     * @return string
     */
    private function getFormatBlock($messages, $style, $large = false)
    {
        if (!$this->formatter instanceof FormatterHelper) {
            $this->formatter = $this->getHelper('formatter');
        }

        return $this->formatter->formatBlock($messages, $style, $large);
    }

    /**
     * Show Error Message
     *
     * @param $message
     */
    private function showError($message)
    {
        $this->output->writeln("\n".$this->getFormatBlock($message, 'error', true));
    }
}
