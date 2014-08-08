<?php

namespace tsCMS\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use tsCMS\ShopBundle\Interfaces\PriceInterface;
use tsCMS\ShopBundle\Interfaces\TotalInterface;

/**
 * OrderLine
 *
 * @ORM\Table(name="orderline")
 * @ORM\Entity
 */
class OrderLine implements PriceInterface, TotalInterface
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
     * @var mixed
     *
     * @ORM\Column(name="amount", type="decimal")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var mixed
     *
     * @ORM\Column(name="pricePerUnit", type="decimal")
     */
    private $pricePerUnit;

    /**
     * @var mixed
     *
     * @ORM\Column(name="vat", type="decimal")
     */
    private $vat;

    /**
     * @var string
     *
     * @ORM\Column(name="partnumber", type="string", length=255)
     */
    private $partnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="plugin", type="string", length=255)
     */
    private $plugin;

    /**
     * @var string
     *
     * @ORM\Column(name="productId", type="string", length=50)
     */
    private $productId;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="lines")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

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
     * Set amount
     *
     * @param mixed $amount
     * @return OrderLine
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return OrderLine
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
     * Set pricePerUnit
     *
     * @param mixed $pricePerUnit
     * @return OrderLine
     */
    public function setPricePerUnit($pricePerUnit)
    {
        $this->pricePerUnit = $pricePerUnit;

        return $this;
    }

    /**
     * Get pricePerUnit
     *
     * @return mixed
     */
    public function getPricePerUnit()
    {
        return $this->pricePerUnit;
    }

    /**
     * Set vat
     *
     * @param mixed $vat
     * @return OrderLine
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return mixed
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set partnumber
     *
     * @param string $partnumber
     * @return OrderLine
     */
    public function setPartnumber($partnumber)
    {
        $this->partnumber = $partnumber;

        return $this;
    }

    /**
     * Get partnumber
     *
     * @return string 
     */
    public function getPartnumber()
    {
        return $this->partnumber;
    }

    /**
     * Set plugin
     *
     * @param string $plugin
     * @return OrderLine
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return string 
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Set productId
     *
     * @param string $productId
     * @return OrderLine
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return string 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function getTotal()
    {
        return $this->getAmount() * $this->getPricePerUnit();
    }

    public function getTotalVat()
    {
        return $this->getTotal() * (100 + $this->getVat()) / 100;
    }

    public function getProductPrice()
    {
        return $this->getPricePerUnit();
    }

    public function getProductPriceVat()
    {
        return $this->getPricePerUnit()  * (100 + $this->getVat()) / 100;
    }
}
