<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

/**
 * Lead
 *
 * @ORM\Table(name="araneum_leads")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\LeadRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Lead
{
    use DateTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=45)
     * @Constraints\Length(min="2", max="45")
     * @Constraints\Regex(pattern="/^\w[\w-' \d]+$/ui")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=45)
     * @Constraints\Length(min="2", max="45")
     * @Constraints\Regex(pattern="/^\w[\w-' \d]+$/ui")
     */
    private $lastName;

    /**
     * @var integer
     *
     * @ORM\Column(name="country", type="smallint", length=4)
     * @Constraints\Regex(pattern="/^\d{0,4}$/")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     * @Constraints\Regex(pattern="/^\d[0-9\-\(\)]{9,17}$/")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Constraints\Length(min="2", max="255")
     * @Constraints\Email()
     */
    private $email;

    //TODO delete appKey field
    /**
     * @var string
     *
     * @ORM\Column(type="string", name="app_key", length=70)
     */
    private $appKey;

    /**
     * @var Application
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Application", inversedBy="leads")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    private $application;

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
     * Set firstName
     *
     * @param  string $firstName
     * @return Lead
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param  string $lastName
     * @return Lead
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set country
     *
     * @param  integer $country
     * @return Lead
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return integer
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phone
     *
     * @param  string $phone
     * @return Lead
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param  string $email
     * @return Lead
     */
    public function setEmail($email)
    {
        $this->email = strtolower($email);

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return strtolower($this->email);
    }

    /**
     * Set appKey
     *
     * @param  string $appKey
     * @return Lead $this
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;

        return $this;
    }

    /**
     * Get appKey
     *
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * Set application
     *
     * @param Application $application
     * @return Lead
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}
