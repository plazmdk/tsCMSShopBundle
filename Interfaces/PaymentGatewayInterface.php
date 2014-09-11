<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/16/14
 * Time: 9:56 PM
 */

namespace tsCMS\ShopBundle\Interfaces;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Model\PaymentAuthorize;
use tsCMS\ShopBundle\Model\PaymentCapture;
use tsCMS\ShopBundle\Model\GatewayUrls;
use tsCMS\ShopBundle\Model\PaymentRefund;
use tsCMS\ShopBundle\Model\PaymentResult;
use tsCMS\ShopBundle\Model\PaymentStatus;

interface PaymentGatewayInterface {
    public function __construct($options);

    public function getName();
    public function getDescription();
    public function getOptionForm(FormBuilderInterface $formBuilder);


    public function getAuthorizeForm(FormBuilderInterface $formBuilder, PaymentAuthorize $authorize, Order $order);

    /**
     * @param Order $order
     * @param PaymentCapture $capture
     * @return PaymentResult
     */
    public function capture(Order $order, PaymentCapture $capture);

    /**
     * @param Order $order
     * @param PaymentRefund $refund
     * @return PaymentResult
     */
    public function refund(Order $order, PaymentRefund $refund);

    /**
     * @param Order $order
     * @return PaymentStatus
     */
    public function status(Order $order);

    /**
     * @param Order $order
     * @param Request $request
     * @return PaymentResult
     */
    public function callback(Order $order, Request $request);

    /**
     * @return boolean
     */
    public function allowManualStatusChange();

    /**
     * @param Order $order
     * @return int
     */
    public function possibleCaptureAmount(Order $order);

    public function calculatePrice(Order $order);
}
