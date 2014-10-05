<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 6/2/14
 * Time: 9:54 PM
 */

namespace tsCMS\ShopBundle\Model;



use tsCMS\TemplateBundle\Entity\Template;

class Config {
    const PRODUCT_URL = "tsCMS_shop_productUrl";
    const BASKET_ROUTE_NAME = "basket";

    const SINGLE_PAGE_CHECKOUT = "singlePageCheckout";
    const CHECKOUT_ROUTE_NAME = "checkout";
    const SELECT_SHIPMENT_ROUTE_NAME = "selectShipment";
    const SELECT_PAYMENT_ROUTE_NAME = "selectPayment";
    const CONFIRM_ORDER_ROUTE_NAME = "confirmOrder";
    const APPROVED_PAYMENT_ROUTE_NAME = "approvedPayment";
    const FAILED_PAYMENT_ROUTE_NAME = "failedPayment";
    const PAYMENT_CALLBACK_ROUTE_NAME = "paymentCallback";
    const OPEN_CART_ROUTE_NAME = "openCart";


    private $singlePageCheckout;

    private $productUrl;
    private $basketUrl;
    private $checkoutUrl;
    private $selectShipmentUrl;
    private $selectPaymentUrl;
    private $confirmOrderUrl;
    private $approvedPaymentUrl;
    private $failedPaymentUrl;
    private $paymentCallbackUrl;
    private $openCartUrl;

    const CONFIRMATION_TEMPLATE = "tsCMS_shop_confirmationTemplate";
    const INVOICE_TEMPLATE = "tsCMS_shop_invoiceTemplate";
    private $orderConfirmationTemplate;
    private $orderInvoiceTemplate;

    const SHOP_NAME = "tsCMS_shop_name";
    const SHOP_EMAIL = "tsCMS_shop_email";
    private $shopName;
    private $shopEmail;

    const SHIPMENT_REQUIRE_MATCH = "tsCMS_shop_shipmentRequireMatch";
    const SHIPMENT_FALLBACK_METHOD = "tsCMS_shop_shipmentFallbackMethod";
    private $shipmentRequireMatch;
    private $shipmentFallbackMethod;

    /**
     * @param mixed $singlePageCheckout
     */
    public function setSinglePageCheckout($singlePageCheckout)
    {
        $this->singlePageCheckout = $singlePageCheckout;
    }

    /**
     * @return mixed
     */
    public function getSinglePageCheckout()
    {
        return $this->singlePageCheckout;
    }

    /**
     * @param mixed $basketUrl
     */
    public function setBasketUrl($basketUrl)
    {
        $this->basketUrl = $basketUrl;
    }

    /**
     * @return mixed
     */
    public function getBasketUrl()
    {
        return $this->basketUrl;
    }

    /**
     * @param mixed $checkoutUrl
     */
    public function setCheckoutUrl($checkoutUrl)
    {
        $this->checkoutUrl = $checkoutUrl;
    }

    /**
     * @return mixed
     */
    public function getCheckoutUrl()
    {
        return $this->checkoutUrl;
    }

    /**
     * @param mixed $productUrl
     */
    public function setProductUrl($productUrl)
    {
        $this->productUrl = $productUrl;
    }

    /**
     * @return mixed
     */
    public function getProductUrl()
    {
        return $this->productUrl;
    }

    /**
     * @param mixed $approvedPaymentUrl
     */
    public function setApprovedPaymentUrl($approvedPaymentUrl)
    {
        $this->approvedPaymentUrl = $approvedPaymentUrl;
    }

    /**
     * @return mixed
     */
    public function getApprovedPaymentUrl()
    {
        return $this->approvedPaymentUrl;
    }

    /**
     * @param mixed $confirmOrderUrl
     */
    public function setConfirmOrderUrl($confirmOrderUrl)
    {
        $this->confirmOrderUrl = $confirmOrderUrl;
    }

    /**
     * @return mixed
     */
    public function getConfirmOrderUrl()
    {
        return $this->confirmOrderUrl;
    }

    /**
     * @param mixed $failedPaymentUrl
     */
    public function setFailedPaymentUrl($failedPaymentUrl)
    {
        $this->failedPaymentUrl = $failedPaymentUrl;
    }

    /**
     * @return mixed
     */
    public function getFailedPaymentUrl()
    {
        return $this->failedPaymentUrl;
    }

    /**
     * @param mixed $selectShipmentUrl
     */
    public function setSelectShipmentUrl($selectShipmentUrl)
    {
        $this->selectShipmentUrl = $selectShipmentUrl;
    }

    /**
     * @return mixed
     */
    public function getSelectShipmentUrl()
    {
        return $this->selectShipmentUrl;
    }



    /**
     * @param mixed $selectPaymentUrl
     */
    public function setSelectPaymentUrl($selectPaymentUrl)
    {
        $this->selectPaymentUrl = $selectPaymentUrl;
    }

    /**
     * @return mixed
     */
    public function getSelectPaymentUrl()
    {
        return $this->selectPaymentUrl;
    }

    /**
     * @param mixed $paymentCallbackUrl
     */
    public function setPaymentCallbackUrl($paymentCallbackUrl)
    {
        $this->paymentCallbackUrl = $paymentCallbackUrl;
    }

    /**
     * @return mixed
     */
    public function getPaymentCallbackUrl()
    {
        return $this->paymentCallbackUrl;
    }

    /**
     * @param mixed $openCartUrl
     */
    public function setOpenCartUrl($openCartUrl)
    {
        $this->openCartUrl = $openCartUrl;
    }

    /**
     * @return mixed
     */
    public function getOpenCartUrl()
    {
        return $this->openCartUrl;
    }

    /**
     * @param Template $orderConfirmationTemplate
     */
    public function setOrderConfirmationTemplate($orderConfirmationTemplate)
    {
        $this->orderConfirmationTemplate = $orderConfirmationTemplate;
    }

    /**
     * @return Template
     */
    public function getOrderConfirmationTemplate()
    {
        return $this->orderConfirmationTemplate;
    }

    /**
     * @param Template $orderInvoiceTemplate
     */
    public function setOrderInvoiceTemplate($orderInvoiceTemplate)
    {
        $this->orderInvoiceTemplate = $orderInvoiceTemplate;
    }

    /**
     * @return Template
     */
    public function getOrderInvoiceTemplate()
    {
        return $this->orderInvoiceTemplate;
    }

    /**
     * @param mixed $shopEmail
     */
    public function setShopEmail($shopEmail)
    {
        $this->shopEmail = $shopEmail;
    }

    /**
     * @return mixed
     */
    public function getShopEmail()
    {
        return $this->shopEmail;
    }

    /**
     * @param mixed $shopName
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;
    }

    /**
     * @return mixed
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * @param mixed $shipmentFallbackMethod
     */
    public function setShipmentFallbackMethod($shipmentFallbackMethod)
    {
        $this->shipmentFallbackMethod = $shipmentFallbackMethod;
    }

    /**
     * @return mixed
     */
    public function getShipmentFallbackMethod()
    {
        return $this->shipmentFallbackMethod;
    }

    /**
     * @param mixed $shipmentRequireMatch
     */
    public function setShipmentRequireMatch($shipmentRequireMatch)
    {
        $this->shipmentRequireMatch = $shipmentRequireMatch;
    }

    /**
     * @return mixed
     */
    public function getShipmentRequireMatch()
    {
        return $this->shipmentRequireMatch;
    }


} 