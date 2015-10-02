<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Locale
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_locales")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\LocaleRepository")
 * @package Araneum\Bundle\MainBundle\Entity
 * @UniqueEntity(fields="name")
 * @UniqueEntity(fields="locale")
 */
class Locale
{
    use DateTrait;

    const ORIENT_LFT_TO_RGT = 1;
    const ORIENT_RGT_TO_LFT = 2;
    const LOC_TO_STR        = 'Create';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=20)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=7)
     *
     * @Assert\NotBlank()
     * @Assert\Locale(message="incorrect_locale_format")
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
     * @ORM\Column(type="string", length=30, options={"default":"UTF-8"})
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=30)
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
     * @param int $orientation
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
     * @return int
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

    /**
     * Convert entity to string
     *
     * @return string
     */
    function __toString()
    {
        return $this->name ? $this->name . " (" . $this->locale . ")" : 'Create Application';
    }
}