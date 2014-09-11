<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 6/1/14
 * Time: 10:21 PM
 */

namespace tsCMS\ShopBundle\Services;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\OrderLine;
use tsCMS\ShopBundle\Entity\ProductOrderLine;
use tsCMS\ShopBundle\Event\BasketUpdatedEvent;
use tsCMS\ShopBundle\tsCMSShopEvents;

class BasketService {
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;

    /** @var \Doctrine\ORM\EntityManager  */
    private $entityManager;
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface  */
    private $eventDispatcher;
    /** @var Order */
    private $basket;

    function __construct(Session $session, EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->basket = $this->fetchBasket();
    }

    private function fetchBasket()
    {
        $basketId = $this->session->get("tscms_basket_id");

        if ($basketId) {
            return $this->entityManager->getRepository("tsCMSShopBundle:Order")->find($basketId);
        }

        return null;
    }

    private function initializeBasket()
    {
        if (!$this->basket) {
            $basket = new Order();
            $basket->setCart(true);
            $basket->setDate(new \DateTime());

            $this->basket = $basket;

            $this->entityManager->persist($basket);
            $this->entityManager->flush();

            $this->session->set("tscms_basket_id", $basket->getId());
            $this->session->save();
        }
    }

    public function newOrder() {
        $this->session->remove("tscms_basket_id");
        $this->session->save();
        $this->basket = null;
    }

    public function addLine(OrderLine $line) {
        $this->initializeBasket();
        $updated = false;
        foreach ($this->basket->getLines() as $orderLine) {
            if ($line->sameAs($orderLine) && !$orderLine->isFixedPrice()) {
                $orderLine->setAmount($orderLine->getAmount() + $line->getAmount());
                $updated = true;
            }
        }
        if (!$updated) {
            $this->basket->addLine($line);
        }
        $this->throwUpdateEvent($line);
        $this->save();
    }

    public function removeLine(OrderLine $line) {
        $this->basket->removeLine($line);
        $this->throwUpdateEvent($line);
        $this->save();
    }

    public function updateLine(OrderLine $item) {
        if ($item->getAmount() == 0) {
            $this->removeLine($item);
            return; // remove line saves and throws the event
        }
        $this->throwUpdateEvent($item);
        $this->save();
    }

    /**
     * @return OrderLine[]
     */
    public function getItems() {
        if (!$this->basket) {
            return array();
        }
        return $this->basket->getLines();
    }

    public function getItemCount() {
        if (!$this->basket) {
            return 0;
        }
        return count($this->basket->getLines());
    }

    public function getAmountTotal() {
        if (!$this->basket) {
            return 0;
        }
        $total = 0;
        foreach ($this->basket->getLines() as $line) {
            $total += $line->getAmount();
        }
        return $total;
    }

    public function getTotal() {
        if (!$this->basket) {
            return 0;
        }
        return $this->basket->getTotal();
    }

    public function getTotalVat() {
        if (!$this->basket) {
            return 0;
        }
        return $this->basket->getTotalVat();
    }

    public function getOrder() {
        return $this->basket;
    }

    public function isEmpty() {
        return $this->getItemCount() == 0;
    }

    public function clear() {
        if ($this->basket) {
            $this->basket->setLines(array());
        }
    }

    private function throwUpdateEvent(OrderLine $line) {
        $event = new BasketUpdatedEvent($this->basket, $line);
        $this->eventDispatcher->dispatch(tsCMSShopEvents::BASKET_UPDATED, $event);
    }

    public function save() {
        $this->entityManager->flush();
    }

    public function openCart($id) {
        $this->session->set("tscms_basket_id", $id);
        $this->session->save();
        $this->basket = $this->fetchBasket();
    }
} 