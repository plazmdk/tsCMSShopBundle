<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/23/14
 * Time: 4:11 PM
 */

namespace tsCMS\ShopBundle\Services;

use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\OrderLine;
use tsCMS\ShopBundle\Entity\PaymentMethod;
use tsCMS\ShopBundle\Entity\PaymentTransaction;
use tsCMS\ShopBundle\Entity\ShipmentMethod;
use tsCMS\ShopBundle\Interfaces\PaymentGatewayInterface;
use tsCMS\ShopBundle\Interfaces\ShipmentGatewayInterface;
use tsCMS\ShopBundle\Model\PaymentCapture;
use tsCMS\ShopBundle\Model\PaymentResult;
use tsCMS\ShopBundle\Model\Statuses;

class ShipmentService {
    /** @var \Doctrine\ORM\EntityManager  */
    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function getShipmentMethods() {
        return $this->em->createQuery("SELECT pm FROM tsCMSShopBundle:ShipmentMethod pm WHERE pm.deleted=0 ORDER BY pm.position")->getResult();
    }

    public function getEnabledShipmentMethods() {
        return $this->em->createQuery("SELECT pm FROM tsCMSShopBundle:ShipmentMethod pm WHERE pm.enabled=1 AND pm.deleted=0 ORDER BY pm.position")->getResult();
    }

    /**
     * @param $identifier
     * @param array $options
     * @return ShipmentGatewayInterface
     */
    public function getShipmentGateway($identifier,$options = array()) {
        if ($identifier instanceof ShipmentMethod) {
            $options = $identifier->getOptions();
            $identifier = $identifier->getGateway();
        }
        $className = "tsCMS\\ShopBundle\\ShipmentGateways\\".$identifier;

        $gateway = new $className($options);
        return $gateway;
    }

    public function addShipmentToOrder(Order $order, ShipmentMethod $shipmentMethod) {
        $this->removeShipmentFromOrder($order);

        $gateway = $this->getShipmentGateway($shipmentMethod);

        $orderline = new OrderLine();
        $orderline->setProductId($shipmentMethod->getId());
        $orderline->setPricePerUnit($gateway->calculatePrice($order));
        $orderline->setAmount(1);
        $orderline->setPartnumber("shipment");
        $orderline->setPlugin("Shipment");
        $orderline->setTitle($shipmentMethod->getTitle());
        $orderline->setVat($shipmentMethod->getVatGroup()->getPercentage());
        $order->addLine($orderline);
    }

    private function removeShipmentFromOrder(Order $order) {
        foreach ($order->getLines() as $line) {
            if ($line->getPlugin() == "Shipment") {
                $order->removeLine($line);
            }
        }
    }
} 