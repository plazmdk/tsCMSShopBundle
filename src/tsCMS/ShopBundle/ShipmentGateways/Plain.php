<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 8/2/14
 * Time: 3:59 PM
 */

namespace tsCMS\ShopBundle\ShipmentGateways;


use Symfony\Component\Form\FormBuilderInterface;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Interfaces\ShipmentGatewayInterface;
use tsCMS\ShopBundle\Model\Basket;

class Plain implements ShipmentGatewayInterface {
    private $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getName()
    {
        return "shipmentgateway.plain.name";
    }

    public function getDescription()
    {
        return "shipmentgateway.plain.description";
    }

    public function getOptionForm(FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('productPrice', 'money',array(
                'label' => 'product.price',
                'divisor' => 100,
                'currency' => 'DKK',
                'required' => true,
                'attr' => array(
                    "class" => "priceCalc",
                    "data-price-group" => "price",
                    "data-price-vat" => "false"
                )
            ))
            ->add('productPriceVat', 'money',array(
                'label' => 'product.priceVat',
                'divisor' => 100,
                'currency' => 'DKK',
                'required' => false,
                'attr' => array(
                    "class" => "priceCalc",
                    "data-price-group" => "price",
                    "data-price-vat" => "true"
                )
            ));
    }

    public function calculatePrice(Order $order)
    {
        return $this->options['productPrice'];
    }

    public function calculatePriceVat(Basket $basket) {
        return $this->calculatePrice($basket) ;
    }
}