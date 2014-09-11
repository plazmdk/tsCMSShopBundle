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
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"product" = "ProductOrderLine", "shipment" = "ShipmentOrderLine"})
 */
abstract class OrderLine implements PriceInterface, TotalInterface
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var mixed
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", length=255)
     */
    private $price;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fixedPrice", type="boolean")
     */
    private $fixedPrice = false;

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

    /**
     * @param boolean $fixedPrice
     */
    public function setFixedPrice($fixedPrice)
    {
        $this->fixedPrice = $fixedPrice;
    }

    /**
     * @return boolean
     */
    public function isFixedPrice()
    {
        return $this->fixedPrice;
    }

    public function sameAs($object) {
        return get_class($this) == get_class($object);
    }
}
