<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 6/2/14
 * Time: 9:03 PM
 */

namespace tsCMS\ShopBundle\Interfaces;


interface PriceInterface {
    public function getProductPrice();
    public function getProductPriceVat();
} 