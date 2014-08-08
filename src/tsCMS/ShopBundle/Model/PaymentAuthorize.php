<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/21/14
 * Time: 10:06 PM
 */

namespace tsCMS\ShopBundle\Model;


class PaymentAuthorize {
    private $amount;
    private $currency;
    private $subscription;
    private $gatewayUrls;

    function __construct($amount, $currency, $subscription, GatewayUrls $gatewayUrls)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->subscription = $subscription;
        $this->gatewayUrls = $gatewayUrls;
    }


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
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $subscription
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return mixed
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return \tsCMS\ShopBundle\Model\GatewayUrls
     */
    public function getGatewayUrls()
    {
        return $this->gatewayUrls;
    }


} 