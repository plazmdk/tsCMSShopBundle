<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 5/29/14
 * Time: 10:48 PM
 */

namespace tsCMS\ShopBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Form\ConfigType;
use tsCMS\ShopBundle\Model\Config;
use tsCMS\ShopBundle\Services\ShipmentService;
use tsCMS\SystemBundle\Services\ConfigService;
use tsCMS\SystemBundle\Services\RouteService;

/**
 * @Route("/shop/config")
 */
class ConfigurationController extends Controller {
    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $config = new Config();

        /** @var RouteService $routeService */
        $routeService = $this->get("tsCMS.routeService");

        /** @var ConfigService $configService */
        $configService = $this->get("tsCMS.configService");

        $basketRoute = $routeService->getRouteByName(Config::BASKET_ROUTE_NAME);
        if ($basketRoute) {
            $config->setBasketUrl($basketRoute->getPath());
        }

        $config->setSinglePageCheckout($configService->get(Config::SINGLE_PAGE_CHECKOUT));

        $checkoutRoute = $routeService->getRouteByName(Config::CHECKOUT_ROUTE_NAME);
        if ($checkoutRoute) {
            $config->setCheckoutUrl($checkoutRoute->getPath());
        }

        $selectShipmentRoute = $routeService->getRouteByName(Config::SELECT_SHIPMENT_ROUTE_NAME);
        if ($selectShipmentRoute) {
            $config->setSelectShipmentUrl($selectShipmentRoute->getPath());
        }

        $selectPaymentRoute = $routeService->getRouteByName(Config::SELECT_PAYMENT_ROUTE_NAME);
        if ($selectPaymentRoute) {
            $config->setSelectPaymentUrl($selectPaymentRoute->getPath());
        }

        $confirmOrderRoute = $routeService->getRouteByName(Config::CONFIRM_ORDER_ROUTE_NAME);
        if ($confirmOrderRoute) {
            $config->setConfirmOrderUrl($confirmOrderRoute->getPath());
        }

        $approvePaymentRoute = $routeService->getRouteByName(Config::APPROVED_PAYMENT_ROUTE_NAME);
        if ($approvePaymentRoute) {
            $config->setApprovedPaymentUrl($approvePaymentRoute->getPath());
        }

        $failedPaymentRoute = $routeService->getRouteByName(Config::FAILED_PAYMENT_ROUTE_NAME);
        if ($failedPaymentRoute) {
            $config->setFailedPaymentUrl($failedPaymentRoute->getPath());
        }

        $paymentCallbackRoute = $routeService->getRouteByName(Config::PAYMENT_CALLBACK_ROUTE_NAME);
        if ($paymentCallbackRoute) {
            $config->setPaymentCallbackUrl($paymentCallbackRoute->getPath());
        }

        $openCartRoute = $routeService->getRouteByName(Config::OPEN_CART_ROUTE_NAME);
        if ($openCartRoute) {
            $config->setOpenCartUrl($openCartRoute->getPath());
        }

        $config->setProductUrl($configService->get(Config::PRODUCT_URL));

        $templateRepository = $this->getDoctrine()->getRepository("tsCMSTemplateBundle:Template");

        $confirmationTemplateId = $configService->get(Config::CONFIRMATION_TEMPLATE);
        if ($confirmationTemplateId) {
            $config->setOrderConfirmationTemplate($templateRepository->find($confirmationTemplateId));
        }

        $invoiceTemplateId = $configService->get(Config::INVOICE_TEMPLATE);
        if ($invoiceTemplateId) {
            $config->setOrderInvoiceTemplate($templateRepository->find($invoiceTemplateId));
        }

        $config->setShopName($configService->get(Config::SHOP_NAME));
        $config->setShopEmail($configService->get(Config::SHOP_EMAIL));

        $config->setShipmentRequireMatch($configService->get(Config::SHIPMENT_REQUIRE_MATCH));
        if ($configService->get(Config::SHIPMENT_FALLBACK_METHOD)) {
            $shipmentRepository = $this->getDoctrine()->getRepository("tsCMSShopBundle:ShipmentMethod");
            $config->setShipmentFallbackMethod($shipmentRepository->find($configService->get(Config::SHIPMENT_FALLBACK_METHOD)));
        }

        /** @var ShipmentService $shipmentService */
        $shipmentService = $this->get("tsCMS_shop.shipmentservice");

        $form = $this->createForm(new ConfigType($shipmentService), $config);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $configService->set(Config::SINGLE_PAGE_CHECKOUT, $config->getSinglePageCheckout());
            $routeService->addRoute(Config::BASKET_ROUTE_NAME, "basket", $config->getBasketUrl(), "tsCMSShopBundle:Shop:basket", "shop",array(),array(),false,true);
            $routeService->addRoute(Config::CHECKOUT_ROUTE_NAME, "checkout", $config->getCheckoutUrl(), "tsCMSShopBundle:Shop:checkout", "shop",array(),array(),false,true);
            $routeService->addRoute(Config::SELECT_SHIPMENT_ROUTE_NAME, "selectPayment", $config->getSelectShipmentUrl(), "tsCMSShopBundle:Shop:selectShipment", "shop",array(),array(),false,true);
            $routeService->addRoute(Config::SELECT_PAYMENT_ROUTE_NAME, "selectPayment", $config->getSelectPaymentUrl(), "tsCMSShopBundle:Shop:selectPayment", "shop",array(),array(),false,true);
            $routeService->addRoute(Config::CONFIRM_ORDER_ROUTE_NAME, "confirmOrder", $config->getConfirmOrderUrl(), "tsCMSShopBundle:Shop:confirmOrder", "shop",array(),array(),false,true);
            $routeService->addRoute(Config::APPROVED_PAYMENT_ROUTE_NAME, "approvedPayment", $config->getApprovedPaymentUrl(), "tsCMSShopBundle:Shop:paymentApproved", "shop",array(),array(),false,true);
            $routeService->addRoute(Config::FAILED_PAYMENT_ROUTE_NAME, "failedPayment", $config->getFailedPaymentUrl(), "tsCMSShopBundle:Shop:paymentFailed", "shop",array(),array(),false,true);
            $routeService->addRoute(Config::PAYMENT_CALLBACK_ROUTE_NAME, "paymentCallback", $config->getPaymentCallbackUrl(), "tsCMSShopBundle:Shop:callback", "shop",array(),array(),false,true);
            $routeService->addRoute(Config::OPEN_CART_ROUTE_NAME, "openCart", $config->getOpenCartUrl(), "tsCMSShopBundle:Shop:openCart", "shop",array(),array(),true,true);
            $configService->set(Config::PRODUCT_URL, $config->getProductUrl());
            $configService->set(Config::CONFIRMATION_TEMPLATE, $config->getOrderConfirmationTemplate()->getId());
            $configService->set(Config::INVOICE_TEMPLATE, $config->getOrderInvoiceTemplate()->getId());
            $configService->set(Config::SHOP_NAME, $config->getShopName());
            $configService->set(Config::SHOP_EMAIL, $config->getShopEmail());
            $configService->set(Config::SHIPMENT_REQUIRE_MATCH, $config->getShipmentRequireMatch());
            $configService->set(Config::SHIPMENT_FALLBACK_METHOD, $config->getShipmentFallbackMethod() ? $config->getShipmentFallbackMethod()->getId() : null);
            return $this->redirect($this->generateUrl("tscms_shop_configuration_index"));
        }

        return array(
            "form" => $form->createView()
        );
    }
} 