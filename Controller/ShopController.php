<?php

namespace tsCMS\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\OrderLine;
use tsCMS\ShopBundle\Entity\PaymentTransaction;
use tsCMS\ShopBundle\Entity\Product;
use tsCMS\ShopBundle\Entity\Productlist;
use tsCMS\ShopBundle\Entity\ProductOrderLine;
use tsCMS\ShopBundle\Entity\ShipmentMethod;
use tsCMS\ShopBundle\Entity\ShipmentOrderLine;
use tsCMS\ShopBundle\Form\BasketType;
use tsCMS\ShopBundle\Form\OrderDetailsType;
use tsCMS\ShopBundle\Form\OrderPaymentType;
use tsCMS\ShopBundle\Form\OrderShipmentType;
use tsCMS\ShopBundle\Interfaces\PaymentGatewayInterface;
use tsCMS\ShopBundle\Model\PaymentAuthorize;
use tsCMS\ShopBundle\Model\Config;
use tsCMS\ShopBundle\Model\GatewayUrls;
use tsCMS\ShopBundle\Model\Item;
use tsCMS\ShopBundle\Model\PaymentResult;
use tsCMS\ShopBundle\Model\Statuses;
use tsCMS\ShopBundle\Services\BasketService;
use tsCMS\ShopBundle\Services\PaymentService;
use tsCMS\ShopBundle\Services\ShipmentService;
use tsCMS\ShopBundle\Services\ShopService;
use tsCMS\SystemBundle\Services\ConfigService;
use tsCMS\TemplateBundle\Services\TemplateService;

class ShopController extends Controller
{
    /**
     * @Template()
     */
    public function productAction(Product $product, Request $request)
    {
        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");
        $order = $basket->getOrder();

        $fixedShipmentLine = null;
        if ($order) {
            foreach ($order->getLines() as $line) {
                if ($line instanceof ShipmentOrderLine && $line->isFixedPrice()) {
                    $fixedShipmentLine = $line;
                }
            }
        }

        $buyFormBuilder = $this->createFormBuilder(array("amount" => 1));
        $buyFormBuilder->add("amount", "number", array(
            "label" => "basket.amount"

        ));
        $buyFormBuilder->add("buy","submit", array(
            "label" => "basket.buy"
        ));
        if ($fixedShipmentLine) {
            $buyFormBuilder->add("overrideFixedPriceShipment", "checkbox", array(
                "label" => "basket.overrideFixedPriceShipment",
                "required" => true
            ));
        }

        $buyForm = $buyFormBuilder->getForm();
        $buyForm->handleRequest($request);
        if ($buyForm->isValid()) {
            if ($fixedShipmentLine) {
                $basket->removeLine($fixedShipmentLine);
            }

            $productOrderLine = new ProductOrderLine();
            $productOrderLine->setProduct($product);
            $productOrderLine->setAmount($buyForm->getData()["amount"]);
            $productOrderLine->setPrice($product->getPrice());

            $basket->addLine($productOrderLine);

            return $this->redirect($this->generateUrl($request->get("_route")));
        }

        return array(
            "product" => $product,
            "buy_form" => $buyForm->createView()
        );
    }

    /**
     * @Template()
     */
    public function productlistAction(Productlist $productlist, Request $request) {
        /** @var ShopService $shopService */
        $shopService = $this->get("tsCMS_shop.shopservice");
        return array(
            "productlist" => $productlist,
            "products" => $shopService->getProductlistProducts($productlist, $productlist->getPagination())
        );
    }
    /**
     * @Template()
     */
    public function basketAction(Request $request)
    {
        /** @var BasketService $basketService */
        $basketService = $this->get("tsCMS_shop.basketservice");
        $order = $basketService->getOrder();

        if (!$order) {
            $order = new Order();
        }

        if ($request->isMethod("POST")) {
            $lines = $order->getLines();

            // If updating a single row (by ajax)
            if ($request->request->get("single", false)) {
                $key = $request->request->get("key");
                $value = $request->request->get("value");
                if (isset($lines[$key]) && !$lines[$key]->isFixedPrice()) {
                    $line = $lines[$key];
                    $line->setAmount(intval($value));
                    $basketService->updateLine($line);

                    if ($line->getAmount() > 0) {
                        // Item was updated - refresh view
                        return $this->render("tsCMSShopBundle:Shop:item.html.twig",array("item" => $line, "key" => $key));
                    }
                }
                // the item was not found or removed
                return new Response();
            }

            $amounts = $request->request->get("amounts",array());

            foreach ($amounts as $key => $amount) {
                if (isset($lines[$key]) && !$lines[$key]->isFixedPrice()) {
                    $line = $lines[$key];
                    $line->setAmount(intval($amount));
                    $basketService->updateLine($line);
                }
            }

            return $this->redirect($this->generateUrl(Config::BASKET_ROUTE_NAME));
        }

        return array(
            "basket" => $order
        );
    }

    /**
     * @Template()
     */
    public function checkoutAction(Request $request)
    {
        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");

        if ($basket->isEmpty()) {
            return $this->redirect("/");
        }

        $form = $this->createForm(new OrderDetailsType(), $basket->getOrder());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $basket->save();
            return $this->redirect($this->generateUrl(Config::SELECT_SHIPMENT_ROUTE_NAME));
        }

        return array(
            "order" => $basket->getOrder(),
            "orderDetailsForm" => $form->createView()
        );
    }

    /**
     * @Template()
     */
    public function selectShipmentAction(Request $request)
    {
        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");

        /** @var ShipmentService $shipmentService */
        $shipmentService = $this->get("tsCMS_shop.shipmentservice");

        if ($basket->isEmpty()) {
            return $this->redirect("/");
        }

        $order = $basket->getOrder();

        $selectedShipmentMethod = null;
        foreach ($order->getLines() as $line) {
            if ($line instanceof ShipmentOrderLine) {
                $selectedShipmentMethod = $line->getShipmentMethod();
            }
        }

        $form = $this->createForm(new OrderShipmentType($shipmentService, $selectedShipmentMethod, $order), $order);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $shipmentService->addShipmentToOrder($basket->getOrder(), $form['shipmentMethod']->getData());
            $basket->save();
            return $this->redirect($this->generateUrl(Config::SELECT_PAYMENT_ROUTE_NAME));
        }


        return array(
            "orderShipmentForm" => $form->createView()
        );
    }

    /**
     * @Template()
     */
    public function selectPaymentAction(Request $request)
    {
        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");

        /** @var PaymentService $paymentService */
        $paymentService = $this->get("tsCMS_shop.paymentservice");

        if ($basket->isEmpty()) {
            return $this->redirect("/");
        }

        $order = $basket->getOrder();

        $form = $this->createForm(new OrderPaymentType($paymentService), $order);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $basket->save();
            return $this->redirect($this->generateUrl(Config::CONFIRM_ORDER_ROUTE_NAME));
        }


        return array(
            "form" => $form->createView()
        );
    }

    /**
     * @Template()
     */
    public function confirmOrderAction() {
        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");
        /** @var PaymentService $paymentService */
        $paymentService = $this->get("tsCMS_shop.paymentservice");

        $order = $basket->getOrder();

        $paymentGateway = $paymentService->getPaymentGateway($order->getPaymentMethod());

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->get('form.factory');
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $formFactory->createNamedBuilder('');

        $gatewayUrls = new GatewayUrls(
            "http".(isset($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$this->generateUrl(Config::PAYMENT_CALLBACK_ROUTE_NAME, array("orderId" => $order->getId())),
            "http".(isset($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$this->generateUrl(Config::FAILED_PAYMENT_ROUTE_NAME),
            "http".(isset($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$this->generateUrl(Config::APPROVED_PAYMENT_ROUTE_NAME)
        );

        $authorize = new PaymentAuthorize($order->getTotalVat(), "DKK", false, $gatewayUrls);
        $paymentGateway->getAuthorizeForm($formBuilder, $authorize, $order);

        $formBuilder->add("_submit", "submit", array(
            "label" => "order.confirm"
        ));
        $form = $formBuilder->getForm();

        return array(
            "order" => $order,
            "form" => $form->createView()
        );
    }

    /**
     */
    public function callbackAction(Request $request) {
        /** @var PaymentService $paymentService */
        $paymentService = $this->get("tsCMS_shop.paymentservice");

        /** @var Order $order */
        $order = $this->getDoctrine()->getRepository("tsCMSShopBundle:Order")->find($request->query->get("orderId"));
        /** @var PaymentGatewayInterface $paymentGateway */
        $paymentGateway = $paymentService->getPaymentGateway($order->getPaymentMethod());

        $result = $paymentGateway->callback($order, $request);

        $orderStatus = Statuses::ORDER_RECEIVED;
        $paymentStatus = Statuses::PAYMENT_NO_STATUS;

        if ($result->getType() == PaymentResult::AUTHORIZE) {
            $paymentStatus = Statuses::PAYMENT_AUTHORIZED;

            if ($result->getCaptured()) {
                $paymentStatus = Statuses::PAYMENT_CAPTURED;
            }
        } else if ($result->getType() == PaymentResult::SUBSCRIPTION) {
            $paymentStatus = Statuses::PAYMENT_SUBSCRIPTION;

            if ($result->getCaptured()) {
                $paymentStatus = Statuses::PAYMENT_SUBSCRIPTION_CAPTURED;
            }
        }

        $order->setStatus($orderStatus);
        $order->setPaymentStatus($paymentStatus);
        $order->setCart(false);

        // add a transaction to the order
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->setType($result->getType());
        $paymentTransaction->setTransactionId($result->getTransactionId());
        $paymentTransaction->setCaptured($result->getCaptured());
        $paymentTransaction->setPaymentMethod($order->getPaymentMethod());
        $order->addPaymentTransaction($paymentTransaction);

        $this->getDoctrine()->getManager()->flush();

        // Send the order confirmation email

        /** @var ConfigService $configService */
        $configService = $this->get("tsCMS.configService");

        $confirmationTemplateId = $configService->get(Config::CONFIRMATION_TEMPLATE);
        if ($confirmationTemplateId) {
            /** @var TemplateService $templateService */
            $templateService = $this->get("tsCMS_template.templateservice");

            $template = $templateService->getTemplate($confirmationTemplateId);
            $mailContent = $templateService->renderTemplate($template, array("order" => $order));

            $mail = \Swift_Message::newInstance($template->getTitle(), $mailContent, "text/html");
            $mail->setTo($order->getCustomerDetails()->getEmail(), $order->getCustomerDetails()->getName());
            $mail->setFrom($configService->get(Config::SHOP_EMAIL),$configService->get(Config::SHOP_NAME));

            /** @var \Swift_Mailer $mailer */
            $mailer = $this->get('mailer');
            $mailer->send($mail);
        }


        if ($result->getRedirect()) {
            return $this->redirect($result->getRedirect());
        }

        return new Response();
    }

    /**
     * @Template()
     */
    public function paymentFailedAction(Request $request) {
        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");
        $order = $basket->getOrder();
        return array(
            "confirmLink" => $this->generateUrl(Config::CONFIRM_ORDER_ROUTE_NAME),
            "order" => $order
        );
    }

    /**
     * @Template()
     */
    public function paymentApprovedAction(Request $request) {
        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");
        $basket->clear();

        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");
        $order = $basket->getOrder();

        if (!$order->getId()) {
            return $this->redirect("/");
        }

        $basket->newOrder();

        return array(
            "order" => $order
        );
    }

    public function openCartAction($id,Request $request) {
        /** @var BasketService $basket */
        $basket = $this->get("tsCMS_shop.basketservice");
        $basket->openCart($id);
        return $this->redirect($this->generateUrl(Config::BASKET_ROUTE_NAME));
    }
}
