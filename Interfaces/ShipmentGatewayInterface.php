<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 8/2/14
 * Time: 3:28 PM
 */

namespace tsCMS\ShopBundle\Interfaces;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormBuilderInterface;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\ProductOrderLine;


abstract class ShipmentGatewayInterface {
    /**
     * @var ArrayCollection
     */
    protected $allowedShipmentMethods;
    protected $options;

    public function __construct($allowedShipmentMethods, $options)
    {
        $this->allowedShipmentMethods = $allowedShipmentMethods;
        $this->options = $options;
    }

    /**
     * Returns the name of the provider (is translatable)
     * @return mixed
     */
    public abstract function getName();

    /**
     * Returns the description of the provider (is translatable)
     * @return mixed
     */
    public abstract function getDescription();

    /**
     * Adds the provider specific fields to the option form
     *
     * @param FormBuilderInterface $formBuilder
     * @return mixed
     */
    public function getOptionForm(FormBuilderInterface $formBuilder)
    {
    }

    /**
     * Specifies wherever the method
     * @return boolean
     */
    public abstract function allowDeliveryAddress();

    /**
     * Returns the path to the twig template for rendering the option form
     * @return mixed
     */
    public function getOptionFormTemplate() {
        return null;
    }

    public abstract function calculatePrice(Order $order);

    /**
     * @param Order $order
     */
    public function getTotalAmountMatchingFreightGroup(Order $order)
    {
        $amount = 0;

        foreach($order->getLines() as $line) {
            if ($line instanceof ProductOrderLine && in_array($line->getProduct()->getShipmentGroup(), $this->allowedShipmentMethods->getValues())) {
                $amount += $line->getAmount();
            }
        }

        return $amount;
    }
} 