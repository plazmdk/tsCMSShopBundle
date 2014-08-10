<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/25/14
 * Time: 11:35 PM
 */

namespace tsCMS\ShopBundle\Model;


class PaymentStatus {
    const AUTHORIZED = "authorized";
    const CAPTURED = "captured";
    const REFUNDED = "refunded";
    const SUBSCRIBED = "subscribed";
    const UNKNOWN = "unknown";

    private $authorizedAmount;
    private $capturedAmount;
    private $refundedAmount;

    private $currentState;

    function __construct($currentState, $authorizedAmount, $capturedAmount, $refundedAmount)
    {
        $this->currentState = $currentState;
        $this->authorizedAmount = $authorizedAmount;
        $this->capturedAmount = $capturedAmount;
        $this->refundedAmount = $refundedAmount;
    }

    /**
     * @return mixed
     */
    public function getAuthorizedAmount()
    {
        return $this->authorizedAmount;
    }

    /**
     * @return mixed
     */
    public function getCapturedAmount()
    {
        return $this->capturedAmount;
    }

    /**
     * @return mixed
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * @return mixed
     */
    public function getRefundedAmount()
    {
        return $this->refundedAmount;
    }


} 