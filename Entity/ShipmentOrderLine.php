<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/1/14
 * Time: 10:05 PM
 */

namespace tsCMS\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="orderline_shipment")
 * @ORM\Entity
 */
class ShipmentOrderLine extends OrderLine {

    /**
     * @var ShipmentMethod
     *
     * @ORM\ManyToOne(targetEntity="ShipmentMethod")
     * @ORM\JoinColumn(name="shipment_id", referencedColumnName="id")
     */
    protected $shipmentMethod;

    /**
     * @param \tsCMS\ShopBundle\Entity\ShipmentMethod $shipmentMethod
     */
    public function setShipmentMethod($shipmentMethod)
    {
        $this->shipmentMethod = $shipmentMethod;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\ShipmentMethod
     */
    public function getShipmentMethod()
    {
        return $this->shipmentMethod;
    }

    public function getTitle() {
        return $this->getShipmentMethod()->getTitle();
    }

    /**
     * @return VatGroup
     */
    public function getVatGroup()
    {
        return $this->getShipmentMethod()->getVatGroup();
    }

    public function setVatGroup(VatGroup $vatGroup)
    {
        throw new \InvalidArgumentException("Trying to set vatgroup on product orderline");
    }

    public function getTotal()
    {
        return $this->getAmount() * $this->getPrice();
    }

    public function getTotalVat()
    {
        return $this->getTotal() * (100 + $this->getVatGroup()->getPercentage()) / 100;
    }
}