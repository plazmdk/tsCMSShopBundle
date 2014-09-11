<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/1/14
 * Time: 10:05 PM
 */

namespace tsCMS\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="orderline_product")
 * @ORM\Entity
 */
class ProductOrderLine extends OrderLine {

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @param \tsCMS\ShopBundle\Entity\Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    public function getTitle() {
        return $this->product->getTitle();
    }

    /**
     * @return VatGroup
     */
    public function getVatGroup()
    {
        return $this->getProduct()->getVatGroup();
    }

    public function setVatGroup(VatGroup $vatGroup)
    {
        throw new \InvalidArgumentException("Trying to set vatgroup on product orderline");
    }

    public function getTotal()
    {
        return $this->getAmount() * $this->getPrice();
    }

    public function getTotalVat()
    {
        return $this->getTotal() * (100 + $this->getVatGroup()->getPercentage()) / 100;
    }

    public function sameAs($object)
    {
        return parent::sameAs($object) && $this->getProduct() === $object->getProduct();
    }


}