<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/16/14
 * Time: 4:05 PM
 */

namespace tsCMS\ShopBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use tsCMS\ShopBundle\Model\Basket;
use tsCMS\ShopBundle\Model\Item;
use tsCMS\SystemBundle\Model\SiteStructureGroup;

class BasketUpdatedEvent extends Event {
    /**
     * @var Basket
     */
    private $basket;
    /**
     * @var Item
     */
    private $updatedItem;

    function __construct(Basket $basket, Item $updatedItem)
    {
        $this->basket = $basket;
        $this->updatedItem = $updatedItem;
    }

    /**
     * @return Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * @return \tsCMS\ShopBundle\Model\Item
     */
    public function getUpdatedItem()
    {
        return $this->updatedItem;
    }

} 