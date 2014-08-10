<?php

namespace tsCMS\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VatGroup
 *
 * @ORM\Table(name="vatgroup")
 * @ORM\Entity
 */
class VatGroup
{
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var mixed
     *
     * @ORM\Column(name="percentage", type="decimal", scale=3)
     */
    private $percentage;


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
     * Set title
     *
     * @param string $title
     * @return VatGroup
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
     * Set percentage
     *
     * @param mixed $percentage
     * @return VatGroup
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get percentage
     *
     * @return mixed
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    public function __toString() {
        return $this->getTitle()." (".$this->getPercentage()."%)";
    }
}
