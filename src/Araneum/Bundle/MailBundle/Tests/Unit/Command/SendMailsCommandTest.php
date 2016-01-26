<?php
namespace Araneum\Bundle\MailBundle\Tests\Unit\Command;

use Araneum\Bundle\MailBundle\Repository\MailRepository;
use Araneum\Bundle\MailBundle\Repository\MailLogRepository;
use Araneum\Bundle\MailBundle\Command\SendMailsCommand;
use Araneum\Bundle\MailBundle\Entity\Mail;
use Araneum\Bundle\MailBundle\Entity\MailLog;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use \Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SendMailsCommandTest
 *
 * @package Araneum\Bundle\MailBundle\Tests\Unit\Command
 */
class SendMailsCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Swift_Transport_SpoolTransport
     */
    protected $transport;

    /**
     * @var CountableMemorySpool
     */
    protected $spool;

    /**
     * Setup testing
     */
    protected function setUp()
    {
        $this->spool = new CountableMemorySpool();
        $this->transport = new \Swift_Transport_SpoolTransport(
            new \Swift_Events_SimpleEventDispatcher(),
            $this->spool
        );
        $this->mailer = new \Swift_Mailer($this->transport);
    }

    /**
     * Test send one emails.
     */
    function testSend()
    {
        $message = \Swift_Message::newInstance();
        $this->assertInstanceOf('Swift_Message', $message);
        $message->setSubject('Test Subject')
            ->setFrom('testsender@test.test')
            ->setTo('testtargetr@test.test')
            ->setCharset('UTF-8')
            ->setContentType('text/html')
            ->setBody("<p>Test Body Html</p>", 'text/html')
            ->addPart("Test text body", 'text/plain');
        $this->mailer->send($message);
        $this->assertCount(1, $this->spool->getMessages(), 'should have sent one email');
        $msg = $this->spool->getMessages()[0];
        $this->assertArrayHasKey('testtargetr@test.test', $msg->getTo());
    }
}
