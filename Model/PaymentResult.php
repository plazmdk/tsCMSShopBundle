<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/23/14
 * Time: 12:20 PM
 */

namespace tsCMS\ShopBundle\Model;


class PaymentResult {
    const ERROR = "error";
    const AUTHORIZE = "authorize";
    const SUBSCRIPTION = "subscription";
    const REFUND = "refund";
    const UNKNOWN = "unknown";

    private $type;
    private $captured = false;

    private $transactionId;

    private $message;

    private $redirect = null;

    /**
     * @param boolean $captured
     */
    public function setCaptured($captured)
    {
        $this->captured = $captured;
    }

    /**
     * @return boolean
     */
    public function getCaptured()
    {
        return $this->captured;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null $redirect
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @return null
     */
    public function getRedirect()
    {
        return $this->redirect;
    }


}