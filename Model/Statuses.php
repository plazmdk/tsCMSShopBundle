<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/23/14
 * Time: 1:47 PM
 */

namespace tsCMS\ShopBundle\Model;


class Statuses {
    const ORDER_RECEIVED = "received";
    const ORDER_DONE = "done";
    const ORDER_REJECTED = "rejected";

    public static $orderStatus = array(
        self::ORDER_RECEIVED    => "orderstatus.received",
        self::ORDER_DONE        => "orderstatus.done",
        self::ORDER_REJECTED    => "orderstatus.rejected"
    );

    const PAYMENT_NO_STATUS = "no_status";
    const PAYMENT_AUTHORIZED = "authorized";
    const PAYMENT_CAPTURED = "captured";
    const PAYMENT_SUBSCRIPTION = "subscription";
    const PAYMENT_SUBSCRIPTION_CAPTURED = "subscription_captured";
    const PAYMENT_FAILED = "failed";

    public static $paymentStatus = array(
        self::PAYMENT_NO_STATUS                 => "paymentstatus.no_status",
        self::PAYMENT_AUTHORIZED                => "paymentstatus.authorized",
        self::PAYMENT_CAPTURED                  => "paymentstatus.captured",
        self::PAYMENT_SUBSCRIPTION              => "paymentstatus.subscription",
        self::PAYMENT_SUBSCRIPTION_CAPTURED     => "paymentstatus.subscription_captured",
        self::PAYMENT_FAILED                    => "paymentstatus.failed"
    );
}