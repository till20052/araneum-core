<?php
/**
 * Author: andreyp
 * Date: 17.09.15
 * Time: 10:26
 * Version 1.0.0
 */

namespace Entity;
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
    protected $option;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;
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
     */
    public function setId($id)
    {
        $this->id = $id;
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function setOption($option)
    {
        $this->option = $option;
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
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     */
    public function setEnabled($enabled=true)
    {
        $this->enabled = $enabled;
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
     */
    public function setDefault($default=true)
    {
        $this->default = $default;
    }

}