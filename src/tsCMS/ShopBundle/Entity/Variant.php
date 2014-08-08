<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 5/19/14
 * Time: 7:41 PM
 */

namespace tsCMS\ShopBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="variant")
 * @ORM\Entity
 */
class Variant {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    /**
     * @ORM\Column(type="string")
     */
    protected $displayTitle;
    /**
     * @ORM\OneToMany(targetEntity="VariantOption", mappedBy="variant", cascade={"persist"})
     */
    protected $options;

    public function __construct() {
        $this->options = new ArrayCollection();
    }

    /**
     * @param mixed $displayTitle
     */
    public function setDisplayTitle($displayTitle)
    {
        $this->displayTitle = $displayTitle;
    }

    /**
     * @return mixed
     */
    public function getDisplayTitle()
    {
        return $this->displayTitle;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param VariantOption $option
     */
    public function addOption($option)
    {
        $this->options->add($option);
        $option->setVariant($this);
    }

    /**
     * @param VariantOption $option
     */
    public function removeOption($option)
    {
        $this->options->removeElement($option);
        $option->setVariant(null);
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }
} 