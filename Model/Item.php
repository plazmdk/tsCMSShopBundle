<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 6/1/14
 * Time: 9:50 PM
 */

namespace tsCMS\ShopBundle\Model;


use tsCMS\ShopBundle\Entity\VatGroup;
use tsCMS\ShopBundle\Interfaces\PriceInterface;
use tsCMS\ShopBundle\Interfaces\TotalInterface;

class Item implements PriceInterface, TotalInterface {
    private $partnumber;
    private $title;
    private $amount;
    private $productPrice;
    private $vat;

    private $plugin;
    private $productId;

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @param mixed $productPrice
     */
    public function setProductPrice($productPrice)
    {
        $this->productPrice = $productPrice;
    }

    /**
     * @return mixed
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

    public function getProductPriceVat() {
        return $this->productPrice * (100 + $this->getVat()) / 100;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
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
     * @param mixed $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return mixed
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * @param float $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @return float
     */
    public function getTotal() {
        return $this->getAmount() * $this->getProductPrice();
    }

    /**
     * @return float
     */
    public function getTotalVat() {
        return $this->getTotal() * (100 + $this->getVat()) / 100;
    }

}