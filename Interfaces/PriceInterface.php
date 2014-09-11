<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 8/31/14
 * Time: 8:12 PM
 */

namespace tsCMS\ShopBundle\Interfaces;


use tsCMS\ShopBundle\Entity\VatGroup;

interface PriceInterface {
    public function getPrice();
    public function setPrice($price);

    /**
     * @return VatGroup
     */
    public function getVatGroup();
    public function setVatGroup(VatGroup $vatGroup);
} 