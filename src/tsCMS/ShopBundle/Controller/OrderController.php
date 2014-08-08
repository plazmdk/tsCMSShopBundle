<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/20/14
 * Time: 4:14 PM
 */

namespace tsCMS\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Form\OrderCustomerDetailsType;
use tsCMS\ShopBundle\Form\OrderLinesType;
use tsCMS\ShopBundle\Form\OrderStatusType;
use tsCMS\ShopBundle\Model\PaymentCapture;
use tsCMS\ShopBundle\Model\PaymentResult;
use tsCMS\ShopBundle\Model\Statuses;
use tsCMS\ShopBundle\Services\PaymentService;

/**
 * @Route("/shop/order")
 */
class OrderController extends Controller {

    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        /** @var Order[] $orders */
        $orders = $this->getDoctrine()->getRepository("tsCMSShopBundle:Order")->findBy(array("status" => Statuses::ORDER_RECEIVED));

        if ($request->isMethod("POST")) {
            /** @var PaymentService $paymentService */
            $paymentService = $this->get("tsCMS_shop.paymentservice");

            $successCount = 0;

            $handleOrders = $request->request->get("orders",array());
            foreach ($orders as $order) {
                if (in_array($order->getId(), $handleOrders)) {
                    if ($order->getPaymentStatus() == Statuses::PAYMENT_AUTHORIZED) {
                        $capture = new PaymentCapture($order->getTotalVat());
                        $result = $paymentService->capture($order, $capture);

                        if ($result->getType() == PaymentResult::AUTHORIZE && $result->getCaptured()) {
                            $order->setStatus(Statuses::ORDER_DONE);
                            $this->getDoctrine()->getManager()->flush();
                            $successCount += 1;

                        } else {

                        }
                    } else if ($order->getPaymentStatus() == Statuses::PAYMENT_CAPTURED) {
                        $order->setStatus(Statuses::ORDER_DONE);
                        $this->getDoctrine()->getManager()->flush();
                        $successCount += 1;
                    }

                }
            }

            return $this->redirect($this->generateUrl("tscms_shop_order_index"));
        }

        return array(
            "orders" => $orders
        );
    }

    /**
     * @Route("/edit/{id}")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Order:order.html.twig")
     */
    public function editAction(Order $order, Request $request) {
        /** @var PaymentService $paymentService */
        $paymentService = $this->get("tsCMS_shop.paymentservice");
        $paymentGateway = $paymentService->getPaymentGateway($order->getPaymentMethod());


        // Order status updating the simple status of the order
        $orderStatusForm = $this->createForm(new OrderStatusType(!$paymentGateway->allowManualStatusChange()), $order);
        $orderStatusForm->handleRequest($request);
        if($orderStatusForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl("tscms_shop_order_edit", array("id" => $order->getId())));
        }

        // Capture form if amount is available
        $captureAmount = $paymentGateway->possibleCaptureAmount($order);
        $captureForm = $this->getCaptureForm($order, $captureAmount);

        // Order customer details - editing details about the customer
        $orderCustomerDetailsForm = $this->createForm(new OrderCustomerDetailsType(), $order);
        $orderCustomerDetailsForm->handleRequest($request);
        if($orderCustomerDetailsForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl("tscms_shop_order_edit", array("id" => $order->getId())));
        }

        // Order lines form - editing notes and lines of the order
        $orderLinesForm = $this->createForm(new OrderLinesType(), $order);
        $orderLinesForm->handleRequest($request);
        if($orderLinesForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl("tscms_shop_order_edit", array("id" => $order->getId())));
        }

        return array(
            "orderStatusForm" => $orderStatusForm->createView(),
            "captureForm" => $captureForm->createView(),
            "orderCustomerDetailsForm" => $orderCustomerDetailsForm->createView(),
            "orderLinesForm" => $orderLinesForm->createView(),
            "order" => $order
        );
    }

    /**
     * @Route("/edit/{id}/capture")
     * @Secure("ROLE_ADMIN")
     */
    public function captureOrderAction(Order $order, Request $request) {
        $form = $this->getCaptureForm($order);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $amount = $form->getData()['amount'];

            if ($amount > 0) {
                /** @var PaymentService $paymentService */
                $paymentService = $this->get("tsCMS_shop.paymentservice");

                $paymentCapture = new PaymentCapture($amount * 100);

                $result = $paymentService->capture($order, $paymentCapture);
                if (!$result->getCaptured()) {

                }
            }

        }
        return $this->redirect($this->generateUrl("tscms_shop_order_edit", array("id" => $order->getId())));
    }

    /**
     * @param $order Order
     * @param $captureAmount
     * @return \Symfony\Component\Form\Form
     */
    private function getCaptureForm(Order $order,$captureAmount = null)
    {
        $captureForm = $this->createFormBuilder(array("amount" => $captureAmount / 100));
        $captureForm->setAction($this->generateUrl("tscms_shop_order_captureorder", array("id" => $order->getId())));
        if ($captureAmount === null || $captureAmount > 0) {
            $captureForm->add("amount", "money", array(
                "label" => "captureAmount",
                "currency" => "DKK"
            ));
            $captureForm->add("capture", "submit", array(
                "label" => "capture"
            ));
        }
        return $captureForm->getForm();
    }
}