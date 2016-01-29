<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\AgentBundle\Entity\Translation\CountryTranslation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Country
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="araneum_customer_countries")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\CountryRepository")
 * @UniqueEntity(fields="name")
 * @Gedmo\TranslationEntity(class="Araneum\Bundle\AgentBundle\Entity\Translation\CountryTranslation")
 * @package Araneum\Bundle\AgentBundle\Entity
 */
class Country
{
    use DateTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="name", unique=true, length=100)
     * @Assert\Length(min=2, max=100)
     */
    protected $name;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", name="title", length=100)
     * @Assert\Length(min=3, max=100)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", name="phone_code", length=20, nullable=true)
     */
    protected $phoneCode;

    /**
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="integer", name="spot_id", length=3, nullable=true)
     */
    protected $spotId;

    /**
     * @ORM\OneToMany(
     *   targetEntity="Araneum\Bundle\AgentBundle\Entity\Translation\CountryTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Country constructor.
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->translations = [];
    }

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
     * @return Country
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
     * Set name
     *
     * @param string $title
     * @return Country
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set phoneCode
     *
     * @param string $phoneCode
     * @return Country
     */
    public function setPhoneCode($phoneCode)
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    /**
     * Get phoneCode
     *
     * @return string
     */
    public function getPhoneCode()
    {
        return $this->phoneCode;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Country
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
     * Get spotId for country
     *
     * @return integer
     */
    public function getSpotId()
    {
        return $this->spotId;
    }

    /**
     * Set spotId for country
     *
     * @param integer $spotId
     * @return Country
     */
    public function setSpotId($spotId)
    {
        $this->spotId = $spotId;

        return $this;
    }

    /**
     * Get countries translation
     *
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Set translation for country
     *
     * @param CountryTranslation $translation
     */
    public function addTranslation(CountryTranslation $translation)
    {
        $this->translations[] = $translation;
        $translation->setObject($this);
    }

    /**
     * Convert entity to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ? $this->name : 'Create Country';
    }
}
