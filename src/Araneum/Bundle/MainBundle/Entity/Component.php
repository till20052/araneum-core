<?php
namespace Araneum\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Component
 * @package Entity
 * @Doctrine\ORM\Mapping\Entity
 * @ORM\Table(name="araneum_components")
 */
class Component
{

    /**
     * @ORM\Column(type="integer")
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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return mixed
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param mixed $option
     * @return mixed
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return mixed
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool|true $enabled
     * @return mixed
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param bool|true $default
     * @return mixed
     */
    public function setDefault($default = true)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @param array
     */
    public function addOption(Array $val){
            $this->option[]=$val;
    }

    /**
     * @param mixed
     * @return mixed
     */
    public function getOptionValueByKey($key){
        if(isset($key) && is_array($this->option)) {

            return $this->option[$key];
        }
    }

    /**
     * @param mixed $key
     */
    public function removeOption($key){
        unset($this->option[$key]);
    }

}