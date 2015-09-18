<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Locale
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_locales")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\LocaleRepository")
 * @package Araneum\Bundle\MainBundle\Entity
 */
class Locale
{
    use \Araneum\BaseBundle\EntityTrait\DateTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $locale;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    protected $enabled;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $orientation;

    /**
     * @ORM\Column(type="string", length=255, options={"default":"UTF-8"})
     */
    protected $encoding;

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
     * Set name
     *
     * @param string $name
     * @return Locale
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return Locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Locale
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    /**
     * Set orientation
     * 
     * @param smallint $orientation
     * @return Locale
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * Get orientation
     *
     * @return smallint
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * Set encoding
     *
     * @param string $encoding
     * @return Locale
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}