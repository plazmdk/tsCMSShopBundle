<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/17/14
 * Time: 5:22 PM
 */

namespace tsCMS\ShopBundle\PaymentGateways;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\VatGroup;
use tsCMS\ShopBundle\Interfaces\PaymentGatewayInterface;
use tsCMS\ShopBundle\Model\PaymentAuthorize;
use tsCMS\ShopBundle\Model\PaymentCapture;
use tsCMS\ShopBundle\Model\GatewayUrls;
use tsCMS\ShopBundle\Model\PaymentRefund;
use tsCMS\ShopBundle\Model\PaymentResult;
use tsCMS\ShopBundle\Model\PaymentStatus;
use tsCMS\ShopBundle\Model\Statuses;

class Plain implements PaymentGatewayInterface {
    private $options = array();

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getName()
    {
        return "paymentgateway.plain.name";
    }

    public function getDescription()
    {
        return "paymentgateway.plain.description";
    }

    public function getOptionForm(FormBuilderInterface $formBuilder)
    {
        $formBuilder->add("fee","tscms_shop_price", array(
            "label" => "paymentgateway.plain.fee",
            "required" => false
        ));
    }

    public function getAuthorizeForm(FormBuilderInterface $formBuilder, PaymentAuthorize $authorize, Order $order)
    {
        $formBuilder->setAction($authorize->getGatewayUrls()->getCallbackUrl());
        $formBuilder->setData(array(
            "redirectUrl" => $authorize->getGatewayUrls()->getSuccessUrl()
        ));
        $formBuilder->add("redirectUrl","hidden");
    }

    public function capture(Order $order, PaymentCapture $capture)
    {
        $result = new PaymentResult();
        if ($order->getPaymentStatus() == Statuses::PAYMENT_CAPTURED) {
            $result->setType(PaymentResult::AUTHORIZE);
            $result->setCaptured(true);
        } else {
            $result->setType(PaymentResult::ERROR);
            $result->setCaptured(false);
        }
        return $result;
    }

    public function refund(Order $order, PaymentRefund $refund)
    {
        return null;
    }

    public function status(Order $order)
    {
        return new PaymentStatus(
            $order->getStatus() == Statuses::PAYMENT_CAPTURED ? PaymentStatus::CAPTURED : PaymentStatus::UNKNOWN,
            $order->getTotalVat(),
            $order->getStatus() == Statuses::PAYMENT_CAPTURED ? $order->getTotalVat() : 0,
            0
        );
    }

    public function callback(Order $order, Request $request)
    {
        $result = new PaymentResult();
        $result->setType(Statuses::PAYMENT_NO_STATUS);
        $result->setRedirect($request->request->get("redirectUrl"));
        return $result;
    }


    /**
     * @return boolean
     */
    public function allowManualStatusChange()
    {
        return true;
    }

    /**
     * @param Order $order
     * @return int
     */
    public function possibleCaptureAmount(Order $order)
    {
        return 0;
    }

    public function calculatePrice(Order $order)
    {
        return $this->options['fee'];
    }
}