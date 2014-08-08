<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 8/2/14
 * Time: 3:28 PM
 */

namespace tsCMS\ShopBundle\Interfaces;


use Symfony\Component\Form\FormBuilderInterface;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Model\Basket;

interface ShipmentGatewayInterface {
    public function __construct($options);

    public function getName();
    public function getDescription();
    public function getOptionForm(FormBuilderInterface $formBuilder);

    public function calculatePrice(Order $order);

} 