<?php
namespace Araneum\Bundle\MailBundle\Tests\Unit\Command;

use Doctrine\ORM\EntityRepository;
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
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class SendMailsCommandTest.
 *
 *
 * @package Araneum\Bundle\MailBundle\Tests\Unit\Command
 */
class SendMailsCommandTest extends WebTestCase
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
     * @var SendMailsCommand
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * Test check command to send emails
     */
    public function testSendEmails()
    {
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                '--limit' => 10,
            ]
        );

        $this->assertEquals(
            1,
            preg_match(
                '/Messange 1 send to mail/',
                $this->commandTester->getDisplay()
            )
        );
    }

    /**
     * Mock EntityMail
     * @return EntityMail
     */
    private function getMockEntityMail()
    {
        $mail = $this->getMockBuilder('Araneum\Bundle\MailBundle\Entity\Mail')
            ->disableOriginalConstructor()
            ->getMock();
        $mail->expects($this->any())
            ->method('getHeadline')
            ->will($this->returnValue('Subject'));
        $mail->expects($this->any())
            ->method('getSender')
            ->will($this->returnValue('testsender@test.com'));
        $mail->expects($this->any())
            ->method('getTarget')
            ->will($this->returnValue('testtarget@test.com'));

        return $mail;
    }

    /**
     * Mock Filesystem
     */
    private function getMockFs()
    {
        $fs = $this->getMockBuilder('\Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $fs->expects($this->any())
            ->method('exists')
            ->will($this->returnValue(false));
    }

    /**
     * Mock Swift_Mailer
     * @param bool $return
     * @return Swift_Mailer
     */
    private function getMockMailer($return)
    {
        $mockMailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();
        $mockMailer->expects($this->any())
            ->method('send')
            ->with($this->anything())
            ->will($this->returnValue($return));

        return $mockMailer;
    }

    /**
     * Mock MailRepository
     * @param int   $limit
     * @param array $return
     * @return MailRepository
     */
    private function getMockMailRepository($limit, array $return)
    {
        $mailRepository = $this->getMockBuilder('Araneum\Bundle\MailBundle\Repository\MailRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $mailRepository->expects($this->any())
            ->method('getAllEmails')
            ->with($limit)
            ->will($this->returnValue($return));

        return $mailRepository;
    }

    /**
     * Mock MailLogRepository
     * @param Mail $mail
     * @return MailLogRepository
     */
    private function getMockMailLogRepository(Mail $mail)
    {
        $mailLogRepositoty = $this->getMockBuilder('Araneum\Bundle\MailBundle\Repository\MailLogRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $mailLogRepositoty->expects($this->any())
            ->method('setMailLog')
            ->with($mail)
            ->will($this->returnValue(true));

        return $mailLogRepositoty;
    }

    /**
     * Mock ObjectManager
     * @param array $setRepository
     * @return ObjectManager
     */
    private function getMockDoctrine($setRepository)
    {
        $mockManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        foreach ($setRepository as $item) {
            $mockManager->expects($this->any())
                ->method('getRepository')
                ->will($this->returnValue($item));
        }
        $doctrine = $this->getMock('Doctrine', array('getManager'));
        $doctrine->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($mockManager));

        return $doctrine;
    }

    /**
     * Mock DI Container
     *
     * @return Container
     */
    private function getContainer()
    {
        $container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('doctrine'))
            ->will($this->returnValue($this->doctrine));
        $container->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('mailer'))
            ->will($this->returnValue($this->mailer));
        $container->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('doctrine'))
            ->will($this->returnValue($this->doctrine));

        return $container;
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $app = new Application();
        $app->add(new SendMailsCommand());

        $this->getMockFs();
        $this->mailer = $this->getMockMailer(true);
        $entityMail = $this->getMockEntityMail();
        $mailRepository = $this->getMockMailRepository(10, array($entityMail));
        $mailLogRepository = $this->getMockMailLogRepository($entityMail);
        $this->doctrine = $this->getMockDoctrine(array($mailRepository, $mailLogRepository));

        /**
         * @var SendMailsCommand command
         */
        $this->command = $app->find('araneum:send:emails');
        $this->command->setContainer($this->getContainer());

        /**
         * @var CommandTester commandTester
         */
        $this->commandTester = new CommandTester($this->command);
    }
}
