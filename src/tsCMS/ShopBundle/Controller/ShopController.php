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
use tsCMS\ShopBundle\Form\BasketType;
use tsCMS\ShopBundle\Form\OrderDetailsType;
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
        $buyFormBuilder = $this->createFormBuilder(array("amount" => 1));

        $buyFormBuilder->add("amount", "number", array(
            "label" => "basket.amount"

        ));
        $buyFormBuilder->add("buy","submit", array(
            "label" => "basket.buy"
        ));
        $buyForm = $buyFormBuilder->getForm();
        $buyForm->handleRequest($request);
        if ($buyForm->isValid()) {
            $item = new Item();
            $item->setAmount(intval($buyForm->getData()["amount"]));
            $item->setProductPrice($product->getPrice());
            $item->setPartnumber($product->getPartnumber());
            $item->setPlugin("Shop");
            $item->setProductId($product->getId());
            $item->setTitle($product->getTitle());
            $item->setVat($product->getVatGroup()->getPercentage());
            /** @var BasketService $basket */
            $basket = $this->get("tsCMS_shop.basketservice");
            $basket->addItem($item);

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
            "products" => $shopService->getProductlistProducts($productlist)
        );
    }
    /**
     * @Template()
     */
    public function basketAction(Request $request)
    {
        /** @var BasketService $basketService */
        $basketService = $this->get("tsCMS_shop.basketservice");
        $basket = $basketService->getBasket();

        if ($request->isMethod("POST")) {
            $items = $basket->getItems();

            // If updating a single row (by ajax)
            if ($request->request->get("single", false)) {
                $key = $request->request->get("key");
                $value = $request->request->get("value");
                if (isset($items[$key])) {
                    $item = $items[$key];
                    $item->setAmount(intval($value));
                    $basketService->updateItem($item);

                    if ($item->getAmount() > 0) {
                        // Item was updated - refresh view
                        return $this->render("tsCMSShopBundle:Shop:item.html.twig",array("item" => $item, "key" => $key));
                    }
                }
                // the item was not found or removed
                return new Response();
            }

            $amounts = $request->request->get("amounts",array());

            foreach ($amounts as $key => $amount) {
                if (isset($items[$key])) {
                    $item = $items[$key];
                    $item->setAmount(intval($amount));
                    $basketService->updateItem($item);
                }
            }

            return $this->redirect($this->generateUrl(Config::BASKET_ROUTE_NAME));
        }

        return array(
            "basket" => $basket
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

        /** @var ShopService $shopService */
        $shopService = $this->get("tsCMS_shop.shopservice");
        $order = $shopService->getOrder();

        foreach ($order->getLines() as $line) {
            $order->removeLine($line);
        }

        foreach ($basket->getItems() as $item) {
            $line = new OrderLine();
            $line->setAmount($item->getAmount());
            $line->setPartnumber($item->getPartnumber());
            $line->setPlugin($item->getPlugin());
            $line->setPricePerUnit($item->getProductPrice());
            $line->setVat($item->getVat());
            $line->setProductId($item->getProductId());
            $line->setTitle($item->getTitle());
            $order->addLine($line);
        }

        $form = $this->createForm(new OrderDetailsType(), $order);
        $form->handleRequest($request);

        if ($form->isValid()) {

            return $this->redirect($this->generateUrl(Config::SELECT_SHIPMENT_ROUTE_NAME));
        }

        return array(
            "order" => $order,
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

        /** @var ShopService $shopService */
        $shopService = $this->get("tsCMS_shop.shopservice");

        /** @var ShipmentService $shipmentService */
        $shipmentService = $this->get("tsCMS_shop.shipmentservice");

        if ($basket->isEmpty()) {
            return $this->redirect("/");
        }

        $order = $shopService->getOrder();
        $form = $this->createForm(new OrderShipmentType(), $order);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $shipmentService->addShipmentToOrder($order, $form['shipmentMethod']->getData());

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

        /** @var ShopService $shopService */
        $shopService = $this->get("tsCMS_shop.shopservice");

        /** @var PaymentService $paymentService */
        $paymentService = $this->get("tsCMS_shop.paymentservice");

        if ($basket->isEmpty()) {
            return $this->redirect("/");
        }

        $order = $shopService->getOrder();
        $formBuilder = $this->createFormBuilder($order);
        $formBuilder->add("paymentMethod","entity", array(
            "class" => "tsCMS\\ShopBundle\\Entity\\PaymentMethod",
            "property" => "title",
            "choices" => $paymentService->getPaymentMethods(),
            "expanded" => true,
            "label" => "paymentmethod.choose"
        ));
        $formBuilder->add("save", "submit", array(
            "label" => "paymentmethod.save"
        ));

        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($order);
            $this->getDoctrine()->getManager()->flush();
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
        /** @var ShopService $shopService */
        $shopService = $this->get("tsCMS_shop.shopservice");
        /** @var PaymentService $paymentService */
        $paymentService = $this->get("tsCMS_shop.paymentservice");

        $order = $shopService->getOrder();

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
        /** @var ShopService $shopService */
        $shopService = $this->get("tsCMS_shop.shopservice");
        $order = $shopService->getOrder();
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

        /** @var ShopService $shopService */
        $shopService = $this->get("tsCMS_shop.shopservice");
        $order = $shopService->getOrder();

        if (!$order->getId()) {
            return $this->redirect("/");
        }

        $shopService->getOrder(true);

        return array(
            "order" => $order
        );
    }
}
