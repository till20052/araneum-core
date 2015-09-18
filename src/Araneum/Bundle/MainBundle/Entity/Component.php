<?php
namespace Araneum\Bundle\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Component
 * @package Entity
 * @Doctrine\ORM\Mapping\Entityoperators
 * @ORM\Entity(repositoryClass="Araneum\Bundle\MainBundle\Repository\ComponentRepository")
 * @ORM\Table(name="araneum_components")
 */
class Component
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $option;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $default;


    public function __construct(){
        $this->setOption(new ArrayCollection());
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
     * @return mixed
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
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set option
     *
     * @param mixed $option
     * @return mixed
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get description
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param mixed $description
     * @return mixed
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     * Set enabled
     *
     * @param bool|true $enabled
     * @return mixed
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;

        return $this;
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
     * @return mixed
     */
    public function setDefault($default = true)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Add option
     *
     * @param array
     */
    public function addOption(array $val)
    {
        foreach($val as $key=>$value){
            $this->option[$key]=$value;
        }
    }

    /**
     * Get option value by key
     *
     * @param mixed
     * @return mixed
     */
    public function getOptionValueByKey($key)
    {
        if (isset($this->option[$key])) {
            return $this->option[$key];
        }else{
            return false;
        }
    }

    /**
     * Remove option by key
     *
     * @param mixed $key
     * @return bool
     */
    public function removeOption($key)
    {
        $result = false;
        if (isset($this->option[$key])) {
            $result = true;
            unset($this->option[$key]);
        }

        return $result;
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
     * Get default
     *
     * @return boolean 
     */
    public function getDefault()
    {
        return $this->default;
    }
}
