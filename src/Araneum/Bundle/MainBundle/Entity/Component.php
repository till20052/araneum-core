<?php

namespace Araneum\Bundle\MainBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Component
 * @package Entity
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ComponentRepository")
 * @ORM\Table(name="araneum_components")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="name")
 */
class Component
{
	use DateTrait;
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank(message="component_name_empty")
	 * @Assert\Length(min=2, max=255, minMessage="component_name_length", maxMessage="component_name_length")
	 */
	protected $name;

	/**
	 * @ORM\Column(type="json_array")
	 */
	protected $options;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $description;

	/**
	 * @var ArrayCollection
	 * @ORM\ManyToMany(targetEntity="Application", mappedBy="components", cascade={"persist"})
	 */
	protected $applications;

	/**
	 * @ORM\Column(type="boolean")
	 * @Assert\Type(type="boolean")
	 */
	protected $enabled;

	/**
	 * @ORM\Column(type="boolean", name="`default`")
	 * @Assert\Type(type="boolean")
	 */
	protected $default;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->setOptions([]);
		$this->setApplications(new ArrayCollection());
	}

	/**
	 * Get id
	 *
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

    /**
     * Set id
     *
     * @param mixed $id
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param mixed $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get option
	 *
	 * @return mixed
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Set option
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;

		return $this;
	}

	/**
	 * Add option
	 *
	 * @param array
	 */
	public function addOption(array $val)
	{
		foreach ($val as $key => $value)
			$this->options[$key] = $value;
	}

	/**
	 * Get option value by key
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function getOptionValueByKey($key)
	{
		if ( ! isset($this->options[$key]))
		{
			return false;
		}

		return $this->options[$key];
	}

	/**
	 * Remove option by key
	 *
	 * @param mixed $key
	 * @return bool
	 */
	public function removeOption($key)
	{
		if ( ! isset($this->options[$key]))
		{
			return false;
		}

		unset($this->options[$key]);

		return true;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set description
	 *
	 * @param mixed $description
	 * @return $this
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get Applications
	 *
	 * @return ArrayCollection
	 */
	public function getApplications()
	{
		return $this->applications;
	}

	/**
	 * Set Applications
	 *
	 * @param ArrayCollection $applications
	 * @return Component
	 */
	public function setApplications(ArrayCollection $applications)
	{
		$this->applications = $applications;

		return $this;
	}

	/**
	 * Get enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Get enabled
	 *
	 * @return boolean
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Set enabled
	 *
	 * @param bool|true $enabled
	 * @return $this
	 */
	public function setEnabled($enabled = true)
	{
		$this->enabled = (bool) $enabled;

		return $this;
	}

	/**
	 * Get default
	 *
	 * @return boolean
	 */
	public function getDefault()
	{
		return $this->default;
	}

	/**
	 * Get default
	 *
	 * @return bool
	 */
	public function isDefault()
	{
		return $this->default;
	}

	/**
	 * Set default
	 *
	 * @param bool|true $default
	 * @return $this
	 */
	public function setDefault($default = true)
	{
		$this->default = (bool) $default;

		return $this;
	}

    /**
     * Get Name of Component
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
