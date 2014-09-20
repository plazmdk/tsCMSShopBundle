<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/20/14
 * Time: 4:14 PM
 */

namespace tsCMS\ShopBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Lexik\Bundle\FormFilterBundle\Tests\Filter\Doctrine\DoctrineQueryBuilderUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\ProductOrderLine;
use tsCMS\ShopBundle\Entity\ShipmentOrderLine;
use tsCMS\ShopBundle\Form\CreateOrderType;
use tsCMS\ShopBundle\Form\OrderCustomerDetailsType;
use tsCMS\ShopBundle\Form\OrderFilterType;
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
        /** @var QueryBuilder $orderQueryBuilder */
        $orderQueryBuilder = $this->getDoctrine()->getRepository("tsCMSShopBundle:Order")->createQueryBuilder("o");
        $orderQueryBuilder->orderBy("o.date");
        $orderQueryBuilder->where("o.cart = 0");

        $orderFilterForm = $this->createForm(new OrderFilterType());
        $orderFilterForm->handleRequest($request);

        if ($orderFilterForm->isValid()) {
            /** @var FilterBuilderUpdaterInterface $filterQueryBuilderUpdater */
            $filterQueryBuilderUpdater = $this->get('lexik_form_filter.query_builder_updater');
            $filterQueryBuilderUpdater->addFilterConditions($orderFilterForm, $orderQueryBuilder);
        } else {
            $orderQueryBuilder->andWhere("o.status = :status")->setParameter("status", Statuses::ORDER_RECEIVED);
        }

        /** @var Order[] $orders */
        $orders = $orderQueryBuilder->getQuery()->getResult();

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
            "filterForm" => $orderFilterForm->createView(),
            "orders" => $orders
        );
    }

    /**
     * @Route("/create")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Order:createOrder.html.twig")
     */
    public function createAction(Request $request) {
        $order = new Order();
        $order->setCart(true);
        $order->setDate(new \DateTime());
        $createForm = $this->createForm(new CreateOrderType(), $order);
        $createForm->handleRequest($request);
        if ($createForm->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($order);
            $manager->flush();

            return $this->redirect($this->generateUrl("tscms_shop_order_edit", array("id" => $order->getId())));
        }

        return array(
            "orderForm" => $createForm->createView()
        );
    }

    /**
     * @Route("/edit/{id}")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Order:order.html.twig")
     */
    public function editAction(Order $order, Request $request) {
        $allowManualChange = true;
        $captureAmount = 0;

        if (!$order->isCart() && $order->getPaymentMethod()) {
            /** @var PaymentService $paymentService */
            $paymentService = $this->get("tsCMS_shop.paymentservice");
            $paymentGateway = $paymentService->getPaymentGateway($order->getPaymentMethod());

            $allowManualChange = $paymentGateway->allowManualStatusChange();
            $captureAmount = $paymentGateway->possibleCaptureAmount($order);
        }


        // Order status updating the simple status of the order
        $orderStatusForm = $this->createForm(new OrderStatusType(!$allowManualChange, $order->isCart()), $order);
        $orderStatusForm->handleRequest($request);
        if($orderStatusForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl("tscms_shop_order_edit", array("id" => $order->getId())));
        }

        // Capture form if amount is available
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