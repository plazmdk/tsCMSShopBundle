<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/16/14
 * Time: 10:29 PM
 */

namespace tsCMS\ShopBundle\Model;


class PaymentRefund {
    private $amount;

    function __construct($amount)
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
} 