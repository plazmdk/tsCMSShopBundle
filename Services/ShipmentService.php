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
use tsCMS\ShopBundle\Entity\ProductOrderLine;
use tsCMS\ShopBundle\Entity\ShipmentMethod;
use tsCMS\ShopBundle\Entity\ShipmentOrderLine;
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

    /**
     * @return ShipmentMethod[]
     */
    public function getShipmentMethods() {
        return $this->em->createQuery("SELECT pm FROM tsCMSShopBundle:ShipmentMethod pm WHERE pm.deleted=0 ORDER BY pm.position")->getResult();
    }

    /**
     * @return ShipmentMethod[]
     */
    public function getEnabledShipmentMethods() {
        return $this->em->createQuery("SELECT pm FROM tsCMSShopBundle:ShipmentMethod pm WHERE pm.enabled=1 AND pm.deleted=0 ORDER BY pm.position")->getResult();
    }

    /**
     * @return ShipmentMethod[]
     */
    public function getPossibleShipmentMethods(Order $order) {
        $enabledMethods = $this->getEnabledShipmentMethods();
        $result = array();

        $requiredGroups = array();
        foreach ($order->getLines() as $line) {
            if ($line instanceof ProductOrderLine) {
                $groupId = $line->getProduct()->getShipmentGroup()->getId();
                if (!in_array($groupId, $requiredGroups)) {
                    $requiredGroups[] = $groupId;
                }
            }
        }

        foreach ($enabledMethods as $enabledMethod) {
            $methodGroupIds = array();
            foreach ($enabledMethod->getShipmentGroups() as $shipmentGroup) {
                $methodGroupIds[] = $shipmentGroup->getId();
            }
            $missing = array_diff($requiredGroups, $methodGroupIds);
            if (count($missing) == 0) {
                $gateway = $this->getShipmentGateway($enabledMethod);
                $enabledMethod->setDeliveryAddressAllowed($gateway->allowDeliveryAddress() ? 1 : 0);
                $result[] = $enabledMethod;
            }
        }

        return $result;
    }

    /**
     * @param $identifier
     * @param array $options
     * @return ShipmentGatewayInterface
     */
    public function getShipmentGateway($identifier,$allowedShipmentMethods = array(), $options = array()) {
        if ($identifier instanceof ShipmentMethod) {
            $allowedShipmentMethods = $identifier->getShipmentGroups();
            $options = $identifier->getOptions();
            $identifier = $identifier->getGateway();
        }
        $className = "tsCMS\\ShopBundle\\ShipmentGateways\\".$identifier;

        $gateway = new $className($allowedShipmentMethods, $options);
        return $gateway;
    }

    public function addShipmentToOrder(Order $order, ShipmentMethod $shipmentMethod) {
        $this->removeShipmentFromOrder($order);

        $gateway = $this->getShipmentGateway($shipmentMethod);

        $orderline = new ShipmentOrderLine();
        $orderline->setShipmentMethod($shipmentMethod);
        $orderline->setAmount(1);
        $orderline->setFixedPrice(false);
        $orderline->setPrice($gateway->calculatePrice($order));

        $order->addLine($orderline);
    }

    private function removeShipmentFromOrder(Order $order) {
        foreach ($order->getLines() as $line) {
            if ($line instanceof ShipmentOrderLine) {
                $order->removeLine($line);
            }
        }
    }
} 