<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 5/18/14
 * Time: 9:44 PM
 */

namespace tsCMS\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use tsCMS\ShopBundle\Interfaces\PriceInterface;
use tsCMS\SystemBundle\Validator\Constraints as tsCMSConstraints;
use tsCMS\SystemBundle\Interfaces\PathInterface;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @tsCMSConstraints\Path()
 */
class Product implements PathInterface, PriceInterface {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $teaser;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $partnumber;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $inventory = null;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $price = 0;
    /**
     * @var VatGroup
     *
     * @ORM\ManyToOne(targetEntity="VatGroup")
     * @ORM\JoinColumn(name="vatGroup_id", referencedColumnName="id")
     */
    protected $vatGroup;
    /**
     * @var ShipmentGroup
     *
     * @ORM\ManyToOne(targetEntity="ShipmentGroup")
     * @ORM\JoinColumn(name="shipmentGroup_id", referencedColumnName="id")
     */
    protected $shipmentGroup;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $weight;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $disabled = false;
    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="product", cascade={"persist","remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $images;
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="variantMasterProduct")
     */
    protected $variantProducts;
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="variantProducts")
     * @ORM\JoinColumn(name="variantMaster_id", referencedColumnName="id")
     */
    protected $variantMasterProduct;
    /**
     * @ORM\OneToMany(targetEntity="ProductVariant", mappedBy="product", cascade={"persist","remove"}, orphanRemoval=true)
     */
    protected $variants;
    /**
     * @ORM\ManyToMany(targetEntity="VariantOption")
     * @ORM\JoinTable(name="product_variantoption",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="variantoption_id", referencedColumnName="id")}
     *      )
     */
    protected $variantOptions;

    /**
     * @ORM\ManyToMany(targetEntity="Category")
     * @ORM\JoinTable(name="product_categories",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     *      )
     */
    protected $categories;

    protected $path;

    public function __construct() {
        $this->variants = new ArrayCollection();
        $this->variantOptions = new ArrayCollection();
        $this->variantProducts = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * @param mixed $teaser
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * @return mixed
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * @return mixed
     */
    public function isDisabled()
    {
        return $this->disabled;
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
     * @param mixed $inventory
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * @return mixed
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param mixed $partnumber
     */
    public function setPartnumber($partnumber)
    {
        $this->partnumber = $partnumber;
    }

    /**
     * @return mixed
     */
    public function getPartnumber()
    {
        return $this->partnumber;
    }

    /**
     * @param Image $image
     */
    public function addImage($image)
    {
        $this->images->add($image);
        $image->setProduct($this);

    }

    /**
     * @param Image $image
     */
    public function removeImage($image)
    {
        $this->images->removeElement($image);
        $image->setProduct(null);
    }

    /**
     * @return Image[]
     */
    public function getImages()
    {
        return $this->images;
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
     * @param Product $variantMasterProduct
     */
    public function setVariantMasterProduct($variantMasterProduct)
    {
        $this->variantMasterProduct = $variantMasterProduct;
    }

    /**
     * @return Product
     */
    public function getVariantMasterProduct()
    {
        return $this->variantMasterProduct;
    }

    /**
     * @param VariantOption $variantOption
     */
    public function addVariantOptions($variantOption)
    {
        $this->variantOptions->add($variantOption);
    }

    /**
     * @param VariantOption $variantOption
     */
    public function removeVariantOptions($variantOption)
    {
        $this->variantOptions->removeElement($variantOption);
    }

    /**
     * @return VariantOption[]
     */
    public function getVariantOptions()
    {
        return $this->variantOptions;
    }

    /**
     * @param Product $variantProducts
     */
    public function addVariantProduct($variantProduct)
    {
        $this->variantProducts->add($variantProduct);
    }

    /**
     * @param Product $variantProduct
     */
    public function removeVariantProduct($variantProduct)
    {
        $this->variantProducts->removeElement($variantProduct);
    }

    /**
     * @return Product[]
     */
    public function getVariantProducts()
    {
        return $this->variantProducts;
    }

    /**
     * @param ProductVariant $variant
     */
    public function addVariant($variant)
    {
        $this->variants->add($variant);
        $variant->setProduct($this);
    }

    /**
     * @param ProductVariant $variant
     */
    public function removeVariant($variant)
    {
        $this->variants->removeElement($variant);
        $variant->setProduct(null);
    }
    /**
     * @return ProductVariant[]
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param VatGroup $vatGroup
     */
    public function setVatGroup(VatGroup $vatGroup)
    {
        $this->vatGroup = $vatGroup;
    }

    /**
     * @return VatGroup
     */
    public function getVatGroup()
    {
        return $this->vatGroup;
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\ShipmentGroup $shipmentGroup
     */
    public function setShipmentGroup($shipmentGroup)
    {
        $this->shipmentGroup = $shipmentGroup;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\ShipmentGroup
     */
    public function getShipmentGroup()
    {
        return $this->shipmentGroup;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return Category[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     */
    public function addCategory($category) {
        if ($this->categories->contains($category)) return;
        $this->categories->add($category);
    }

    /**
     * @param Category $category
     */
    public function removeCategory($category) {
        $this->categories->removeElement($category);
    }



    public function __toString() {
        $name = $this->getTitle();
        if (count($this->getVariantOptions())) {
            foreach ($this->getVariantOptions() as $option) {
                $name .= " - ". $option->getDisplayTitle();
            }
        }
        return $name;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function ensureVariantRules() {
        $masterProduct = $this->getVariantMasterProduct();
        if ($masterProduct) {
            // enforce variant products can not have variants
            if (count($this->getVariants()) > 0) {
                throw new \Exception("It is not possible to have variants on a variant product");
            }

            // enforce variant products to have all variants set and not multiple times
            $requiredVariants = array();
            foreach ($masterProduct->getVariants() as $productVariant) {
                $requiredVariants[] = $productVariant->getVariant();
            }
            $addedOptionVariants = array();
            foreach ($this->getVariantOptions() as $option) {
                if (!in_array($option->getVariant(), $requiredVariants)) {
                    throw new \Exception("The variant product is missing an option");
                }
                if (in_array($option->getVariant(), $addedOptionVariants)) {
                    throw new \Exception("The variant product has an option twice");
                }
                $addedOptionVariants[] = $option->getVariant();
            }
        } else {
            // enfore that master products does not have options
            if (count($this->getVariantOptions()) > 0) {
                throw new \Exception("A master product cannot have variant options");
            }
        }
    }
}