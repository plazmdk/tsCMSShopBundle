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
use PHPExcel_IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\ProductOrderLine;
use tsCMS\ShopBundle\Entity\ShipmentOrderLine;
use tsCMS\ShopBundle\Form\CreateOrderType;
use tsCMS\ShopBundle\Form\OrderCustomerDetailsType;
use tsCMS\ShopBundle\Form\OrderFilterType;
use tsCMS\ShopBundle\Form\OrderLinesType;
use tsCMS\ShopBundle\Form\OrderStatusType;
use tsCMS\ShopBundle\Form\SendEmailType;
use tsCMS\ShopBundle\Model\Config;
use tsCMS\ShopBundle\Model\PaymentCapture;
use tsCMS\ShopBundle\Model\PaymentResult;
use tsCMS\ShopBundle\Model\Statuses;
use tsCMS\ShopBundle\Services\PaymentService;
use tsCMS\SystemBundle\Services\ConfigService;
use tsCMS\TemplateBundle\Services\TemplateService;

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
        $orderQueryBuilder->leftJoin("o.lines","ol");
        $orderQueryBuilder->addSelect("ol");
        $orderQueryBuilder->orderBy("o.date");

        $orderFilterForm = $this->createForm(new OrderFilterType());
        $orderFilterForm->handleRequest($request);

        if ($orderFilterForm->isValid()) {
            /** @var FilterBuilderUpdaterInterface $filterQueryBuilderUpdater */
            $filterQueryBuilderUpdater = $this->get('lexik_form_filter.query_builder_updater');
            $filterQueryBuilderUpdater->addFilterConditions($orderFilterForm, $orderQueryBuilder);
        } else {
            $orderQueryBuilder->andWhere("o.status = :status")->setParameter("status", Statuses::ORDER_RECEIVED);
        }

        $orderQueryBuilder->andWhere("o.cart = 0");

        /** @var Order[] $orders */
        $orders = $orderQueryBuilder->getQuery()->getResult();

        if ($request->isMethod("GET") && $request->query->has("export")) {
            $phpExcel = new \PHPExcel();
            $sheet = $phpExcel->getActiveSheet();
            $maxProductCount = 0;
            foreach (["Ordrenr", "Navn", "Adresse","Postnr + by","Telefon","Leveringsadresse","Leverings postnr + by"] as $index => $column) {
                $sheet->setCellValueByColumnAndRow($index, 1, $column);
                $sheet->getColumnDimensionByColumn($index)->setAutoSize(true);
            }
            foreach ($orders as $rowIndex => $order) {
                $rowNo = $rowIndex + 2;

                $sheet->setCellValueByColumnAndRow(0, $rowNo, $order->getId());
                $sheet->setCellValueByColumnAndRow(1, $rowNo, $order->getCustomerDetails()->getName());
                $sheet->setCellValueByColumnAndRow(2, $rowNo, $order->getCustomerDetails()->getAddress() . " ".$order->getCustomerDetails()->getAddress2());
                $sheet->setCellValueByColumnAndRow(3, $rowNo, $order->getCustomerDetails()->getPostalcode() . " ".$order->getCustomerDetails()->getCity());
                $sheet->setCellValueByColumnAndRow(4, $rowNo, $order->getCustomerDetails()->getPhone());
                if ($order->getShipmentDetails()) {
                    $sheet->setCellValueByColumnAndRow(5, $rowNo, $order->getShipmentDetails()->getAddress() . " ".$order->getShipmentDetails()->getAddress2());
                    $sheet->setCellValueByColumnAndRow(6, $rowNo, $order->getShipmentDetails()->getPostalcode() . " ".$order->getShipmentDetails()->getCity());
                }

                foreach ($order->getLines() as $lineno => $line) {
                    $sheet->setCellValueByColumnAndRow(7 + ($lineno * 3), $rowNo, $line->getAmount());
                    if ($line instanceof ProductOrderLine) {
                        $sheet->setCellValueByColumnAndRow(7 + ($lineno * 3) + 1, $rowNo, $line->getProduct()->getPartnumber());
                    } else {
                        $sheet->setCellValueByColumnAndRow(7 + ($lineno * 3) + 1, $rowNo, "-");
                    }

                    $sheet->setCellValueByColumnAndRow(7 + ($lineno * 3) + 2, $rowNo, $line->getTitle());
                    if ($lineno > $maxProductCount) {
                        $maxProductCount = $lineno;
                    }
                }
            }
            for($a = 0; $a <= $maxProductCount; $a++) {
                $sheet->getColumnDimensionByColumn(7 + ($a * 3))->setAutoSize(true);
                $sheet->getColumnDimensionByColumn(7 + ($a * 3) + 1)->setAutoSize(true);
                $sheet->getColumnDimensionByColumn(7 + ($a * 3) + 2)->setAutoSize(true);
            }
            $sheet->calculateColumnWidths();

            $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');

            ob_start();
            $objWriter->save('php://output');
            $rawExcel = ob_get_clean();


            $response = new StreamedResponse(function () use ($rawExcel) { echo $rawExcel;});

            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Cache-Control', '');
            $response->headers->set('Content-Length', strlen($rawExcel));
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s'));
            $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'orders.xls');
            $response->headers->set('Content-Disposition', $contentDisposition);
            $response->prepare($request);

            return $response;
        }

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

        $sendEmailForm = $this->createForm(new SendEmailType($this->generateUrl("tscms_shop_order_sendconfirmationmail", array("id" => $order->getId()))));


        return array(
            "orderStatusForm" => $orderStatusForm->createView(),
            "captureForm" => $captureForm->createView(),
            "orderCustomerDetailsForm" => $orderCustomerDetailsForm->createView(),
            "orderLinesForm" => $orderLinesForm->createView(),
            "order" => $order,
            "sendConfirmationForm" => $sendEmailForm->createView()
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
                "label" => "capture.amount",
                "currency" => "DKK"
            ));
            $captureForm->add("capture", "submit", array(
                "label" => "capture.perform"
            ));
        }
        return $captureForm->getForm();
    }

    /**
     * @Route("/{id}/sendConfirmationMail")
     * @Secure("ROLE_ADMIN")
     */
    public function sendConfirmationMailAction(Order $order, Request $request) {
        $sendEmailForm = $this->createForm(new SendEmailType(""));
        $sendEmailForm->handleRequest($request);
        if ($sendEmailForm->isValid()) {
            /** @var ConfigService $configService */
            $configService = $this->get("tsCMS.configService");

            $confirmationTemplateId = $configService->get(Config::CONFIRMATION_TEMPLATE);
            if ($confirmationTemplateId) {
                /** @var TemplateService $newsletterService */
                $newsletterService = $this->get("tsCMS_template.templateservice");

                $template = $newsletterService->getTemplate($confirmationTemplateId);
                $mailContent = $newsletterService->renderTemplate($template, array("order" => $order));

                $title = str_replace("order.id", $order->getId(), $template->getTitle());
                $mail = \Swift_Message::newInstance($title, $mailContent, "text/html");
                $mail->setTo($sendEmailForm['email']->getData(), $order->getCustomerDetails()->getName());
                $mail->setFrom($configService->get(Config::SHOP_EMAIL), $configService->get(Config::SHOP_NAME));

                /** @var \Swift_Mailer $mailer */
                $mailer = $this->get('mailer');
                $mailer->send($mail);
            }
        }
        return $this->redirect($this->generateUrl("tscms_shop_order_edit", array("id" => $order->getId())));
    }
}