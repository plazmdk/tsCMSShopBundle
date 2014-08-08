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
 * @ORM\Table(name="productvariant")
 * @ORM\Entity
 */
class ProductVariant {
    /**
     * @var Product
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="variants")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @var Variant
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Variant")
     * @ORM\JoinColumn(name="variant_id", referencedColumnName="id")
     */
    protected $variant;

    /**
     * @var VariantOption[]
     * @ORM\ManyToMany(targetEntity="VariantOption")
     * @ORM\JoinTable(name="productvariant_options",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="product_id"),@ORM\JoinColumn(name="variant_id", referencedColumnName="variant_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="variantoption_id", referencedColumnName="id")}
     *      )
     */
    protected $options;

    public function __construct() {
        $this->options = new ArrayCollection();
    }

    /**
     * @param VariantOption $option
     */
    public function addOption($option)
    {
        $this->options->add($option);
    }

    /**
     * @param VariantOption $option
     */
    public function removeOption($option)
    {
        $this->options->removeElement($option);
    }

    /**
     * @return VariantOption[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
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