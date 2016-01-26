<?php
namespace Araneum\Bundle\MailBundle\Tests\Unit\Command;

/**
 * Class CountableMemorySpool
 * @package Araneum\Bundle\MailBundle\Tests\Unit\Command
 */
final class CountableMemorySpool extends \Swift_MemorySpool implements \Countable
{
    /**
     * Return count get messange to send
     * @return int
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * Return Array massange to sand
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}