<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 6/1/14
 * Time: 10:21 PM
 */

namespace tsCMS\ShopBundle\Services;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use tsCMS\ShopBundle\Event\BasketUpdatedEvent;
use tsCMS\ShopBundle\Model\Basket;
use tsCMS\ShopBundle\Model\Item;
use tsCMS\ShopBundle\tsCMSShopEvents;

class BasketService {
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;
    /** @var Basket */
    private $basket;

    private $eventDispatcher;

    function __construct(Session $session, EventDispatcherInterface $eventDispatcher)
    {
        $this->session = $session;
        $this->basket = $session->get("tsCMS_basket", new Basket());
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addItem(Item $item) {
        $this->basket->addItem($item);
        $this->throwUpdateEvent($item);
        $this->save();
    }

    public function removeItem(Item $item) {
        $this->basket->removeItem($item);
        $this->throwUpdateEvent($item);
        $this->save();
    }

    public function updateItem(Item $item) {
        $this->basket->updateItem($item);
        $this->throwUpdateEvent($item);
        $this->save();
    }

    /**
     * @return Item[]
     */
    public function getItems() {
        return $this->basket->getItems();
    }

    public function getItemCount() {
        return $this->basket->getItemCount();
    }

    public function getAmountTotal() {
        return $this->basket->getAmountTotal();
    }

    public function getTotal() {
        return $this->basket->getTotal();
    }

    public function getTotalVat() {
        return $this->basket->getTotalVat();
    }

    public function getBasket() {
        return $this->basket;
    }

    public function isEmpty() {
        return $this->basket->isEmpty();
    }

    public function clear() {
        $this->basket->clear();
    }

    private function throwUpdateEvent(Item $item) {
        $event = new BasketUpdatedEvent($this->basket, $item);
        $this->eventDispatcher->dispatch(tsCMSShopEvents::BASKET_UPDATED, $event);
    }

    private function save() {
        $this->session->set("tsCMS_basket", $this->basket);
        $this->session->save();
    }
} 