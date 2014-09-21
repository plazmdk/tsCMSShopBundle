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
 * @ORM\Table(name="productlist_product")
 * @ORM\Entity
 */
class ProductlistProduct {
    /**
     * @var Productlist
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Productlist", inversedBy="singleProducts")
     */
    protected $productlist;
    /**
     * @var Product
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product")
     */
    protected $product;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $position;

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

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

    /**
     * @param \tsCMS\ShopBundle\Entity\Productlist $productlist
     */
    public function setProductlist($productlist)
    {
        $this->productlist = $productlist;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\Productlist
     */
    public function getProductlist()
    {
        return $this->productlist;
    }



}