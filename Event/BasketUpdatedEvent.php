<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/16/14
 * Time: 4:05 PM
 */

namespace tsCMS\ShopBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\OrderLine;

class BasketUpdatedEvent extends Event {
    /**
     * @var Order
     */
    private $order;
    /**
     * @var OrderLine
     */
    private $updatedLine;

    function __construct(Order $Order, OrderLine $updatedLine)
    {
        $this->order = $Order;
        $this->updatedLine = $updatedLine;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return OrderLine
     */
    public function getUpdatedLine()
    {
        return $this->updatedLine;
    }

} 