<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/23/14
 * Time: 4:11 PM
 */

namespace tsCMS\ShopBundle\Services;

use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\PaymentMethod;
use tsCMS\ShopBundle\Entity\PaymentTransaction;
use tsCMS\ShopBundle\Interfaces\PaymentGatewayInterface;
use tsCMS\ShopBundle\Model\PaymentCapture;
use tsCMS\ShopBundle\Model\PaymentResult;
use tsCMS\ShopBundle\Model\Statuses;

class PaymentService {
    /** @var \Doctrine\ORM\EntityManager  */
    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getPaymentMethods() {
        return $this->em->createQuery("SELECT pm FROM tsCMSShopBundle:PaymentMethod pm WHERE pm.deleted=0 ORDER BY pm.position")->getResult();
    }

    public function getEnabledPaymentMethods() {
        return $this->em->createQuery("SELECT pm FROM tsCMSShopBundle:PaymentMethod pm WHERE pm.enabled=1 AND pm.deleted=0 ORDER BY pm.position")->getResult();
    }

    /**
     * @param $identifier
     * @param array $options
     * @return PaymentGatewayInterface
     */
    public function getPaymentGateway($identifier,$options = array()) {
        if ($identifier instanceof PaymentMethod) {
            $options = $identifier->getOptions();
            $identifier = $identifier->getGateway();
        }
        $className = "tsCMS\\ShopBundle\\PaymentGateways\\".$identifier;

        $gateway = new $className($options);
        return $gateway;
    }

    /**
     * @param Order $order
     * @param PaymentCapture $capture
     * @return PaymentResult
     */
    public function capture(Order $order, PaymentCapture $capture) {
        $paymentGateway = $this->getPaymentGateway($order->getPaymentMethod());

        $result = $paymentGateway->capture($order, $capture);

        if ($result->getType() == PaymentResult::AUTHORIZE && $result->getCaptured()) {
            $order->setPaymentStatus(Statuses::PAYMENT_CAPTURED);
        }

        // add a transaction to the order
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->setType($result->getType());
        $paymentTransaction->setTransactionId($result->getTransactionId());
        $paymentTransaction->setCaptured($result->getCaptured());
        $paymentTransaction->setPaymentMethod($order->getPaymentMethod());
        $order->addPaymentTransaction($paymentTransaction);

        $this->em->flush();

        return $result;
    }
} 