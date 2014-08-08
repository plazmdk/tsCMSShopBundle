<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 5/19/14
 * Time: 7:41 PM
 */

namespace tsCMS\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="variant_option")
 * @ORM\Entity
 */
class VariantOption {
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
     * @ORM\ManyToOne(targetEntity="Variant", inversedBy="options")
     * @ORM\JoinColumn(name="variant_id", referencedColumnName="id")
     */
    protected $variant;

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

    /**
     * @param Variant $variant
     */
    public function setVariant($variant)
    {
        $this->variant = $variant;
    }

    /**
     * @return Variant
     */
    public function getVariant()
    {
        return $this->variant;
    }


} 