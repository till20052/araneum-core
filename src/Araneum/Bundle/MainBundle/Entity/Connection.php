<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Connection
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_connections")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ConnectionRepository")
 * @package Araneum\Bundle\MainBundle\Entity
 */
class Connection
{
    //DateTrait for created_at, update_at
    //use Araneum\BaseBundle\EntityTrait\DateTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="`type`")
     */
    protected $type;

    /**
     * @ORM\Column(type="string", name="name", unique="true" length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="host" length=100)
     */
    protected $host;

    /**
     * @ORM\Column(type="integer", name="port", length=100)
     */
    protected $port;

    /**
     * @ORM\Column(type="string", name="user_name" length=100)
     */
    protected $user_name;

    /**
     * @ORM\Column(type="string", name="password", length=100)
     */
    protected $password;

    /**
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     */
    protected $updated_at;
}