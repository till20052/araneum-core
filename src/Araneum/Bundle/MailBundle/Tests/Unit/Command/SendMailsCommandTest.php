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


/**
 * Class SendMailsCommandTest
 *
 * @package Araneum\Bundle\MailBundle\Tests\Unit\Command
 */
class SendMailsCommandTest extends \PHPUnit_Framework_TestCase
{

    function testSend() {


    }

}