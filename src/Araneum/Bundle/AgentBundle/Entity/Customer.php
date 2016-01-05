<?php
namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 *
 * @ORM\Table("araneum_customers")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\CustomerRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="email")
 */
class Customer
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
     * @var Application
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Application", inversedBy="customers")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    private $application;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=45, nullable=true)
     * @Assert\Length(min=2, max=45)
     * @Assert\Regex(pattern="/^\w[\w-' \d]+$/ui")
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=45, nullable=true)
     * @Assert\Length(min=2, max=45)
     * @Assert\Regex(pattern="/^\w[\w-' \d]+$/ui")
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="country_id", type="smallint", nullable=true)
     * @Assert\Regex(pattern="/^\d+$/")
     * @Assert\NotBlank()
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     * @Assert\Email()
     * @Assert\Length(max=100)
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=24, nullable=true)
     * @Assert\Length(max=24)
     * @Assert\Regex(pattern="/^\d[0-9\-\(\)]{9,17}$/")
     * @Assert\NotBlank()
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=10)
     * @Assert\NotBlank()
     */
    private $currency;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     */
    private $birthday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="callback", type="boolean")
     */
    private $callback;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="delivered_at", nullable=true)
     */
    private $deliveredAt;

    /**
     * Not saved in DB
     *
     * @var string
     */
    private $password;

    /**
     * Not saved in DB
     *
     * @var int
     */
    private $spotId;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="site_id")
     */
    private $siteId;

    /**
     * Set siteId
     *
     * @param  string $siteId
     * @return Customer
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * Get siteId
     *
     * @return integer
     */
    public function getSiteId()
    {
        return $this->siteId;
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
     * Set application
     *
     * @param  Application $application
     * @return Customer
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

    /**
     * Set firstName
     *
     * @param  string $firstName
     * @return Customer
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
     * @return Customer
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
     * @param  string $country
     * @return Customer
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return int
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set email
     *
     * @param  string $email
     * @return Customer
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
     * Set phone
     *
     * @param  string $phone
     * @return Customer
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
     * Set callback
     *
     * @param  boolean $callback
     * @return Customer
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Get callback
     *
     * @return boolean
     */
    public function isCallback()
    {
        return $this->callback;
    }

    /**
     * Get deliveredAt
     *
     * @return null|\DateTime
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * Set deliveredAt
     *
     * @param  null|\DateTime $deliveredAt
     * @return $this
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return strtoupper($this->currency); // for BC
    }

    /**
     * Set currency
     *
     * @param  string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = strtoupper($currency);

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return mixed
     */
    public function setBirthday(\DateTime $birthday = null)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Customer
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get spotId
     *
     * @return int
     */
    public function getSpotId()
    {
        return $this->spotId;
    }

    /**
     * Set spotId
     *
     * @param int $spotId
     * @return Customer
     */
    public function setSpotId($spotId)
    {
        $this->spotId = $spotId;

        return $this;
    }
}
