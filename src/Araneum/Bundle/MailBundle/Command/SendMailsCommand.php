<?php
/**
 * Created by PhpStorm.
 * User: sergeyn
 * Date: 21.01.16
 * Time: 10:02
 */

namespace Araneum\Bundle\MailBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
// Doctrine
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
// Entity
use Araneum\Bundle\MailBundle\Entity\Mail as EntityMail;
use Araneum\Bundle\MailBundle\Entity\MailLog as MailLog;

class SendMailsCommand extends ContainerAwareCommand
{
    /**
     * Set charset to sendmailer
     * @type string
     */
    const Charset = 'UTF-8';

    /**
     * Set Content Type to sendmailer
     * @type string
     */
    const ContentType = 'text/html';

    /**
     * Set Default limit send mails
     * @type integer
     */
    const DefaultLimit = 10;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var FormatterHelper
     */
    private $formatter;


    protected function configure()
    {
        $this
            ->setName('send:emails')
            ->setDescription('This command auto send all mails!')
            ->addOption(
                'limit',
                '-l',
                InputOption::VALUE_REQUIRED,
                'Who do you want to greet?',
                self::DefaultLimit
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $limit = $input->getOption('limit');
        $this->sendAllMail($limit ? $limit : self::DefaultLimit);
    }

    /**
     * Method send all mails messange.
     *
     * @param $limit
     * @throws \Exception
     */
    public function sendAllMail($limit)
    {
        $messangeCount = 0;

        $manager = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $repository = $manager->getRepository("AraneumMailBundle:Mail");
        $mails = $repository->getAllEmails($limit);

        if(is_array($mails) && count($mails)):

            foreach($mails as $mail):

                $message = \Swift_Message::newInstance()
                    ->setSubject( $mail->getHeadline() )
                    ->setFrom( $mail->getSender() )
                    ->setTo( $mail->getTarget() )
                    ->setCharset( self::Charset )
                    ->setContentType( self::ContentType )
                    ->setBody( $mail->getHtmlBody() ? $mail->getHtmlBody() : $mail->getTextBody() );

                if($this->getContainer()->get('mailer')->send($message)):

                    try {
                        $eMail = $mail->setStatus(EntityMail::STATUS_SENT);
                        $manager->persist($eMail);
                        $manager->flush();
                        $messangeCount++;

                        $this->setLog($mail->getId(), MailLog::SEND_OK);

                    } catch(\Exception $e) {
                        throw new \Exception("Don't save email status. Error:".$e->getMessage());
                    }

                else:
                    $this->setLog($mail->getId(), MailLog::SEND_ERROR);
                    $this->showError("Error! Not send messange to ".$mail->getId());
                endif;

            endforeach;

            $this->output->writeln("Messange $messangeCount send to mail");

        else:
            $this->showError("There is not data to send.");
        endif;
        return True;
    }

    /**
     * Set log sender mails
     *
     * @param $mail_id
     * @param int $status
     */
    private function setLog($mail_id, $status = MailLog::SEND_OK) {
        $manager = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        $manager->getRepository("AraneumMailBundle:MailLog")
            ->setMailLog(['mail_id' => $mail_id, 'status' => $status]);
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

    /**
     * Show Info Message
     *
     * @param $message
     */
    private function showInfo($message)
    {
        $this->output->writeln($this->getFormatBlock($message, 'info'));
    }
}