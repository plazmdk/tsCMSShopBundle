<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 6/2/14
 * Time: 5:59 PM
 */

namespace tsCMS\ShopBundle\Model;


use tsCMS\ShopBundle\Interfaces\TotalInterface;

class Basket implements TotalInterface {
    /** @var Item[] */
    private $content = array();

    private function validate() {
        foreach ($this->content as $key => $value) {
            if ($value->getAmount() <= 0) {
                unset($this->content[$key]);
            }
        }
    }

    public function addItem(Item $item) {
        $key = $this->getItemKey($item);
        if (isset($this->content[$key])) {
            $item->setAmount($this->content[$key]->getAmount() + $item->getAmount());
        }
        $this->content[$key] = $item;
        $this->validate();
    }

    public function removeItem(Item $item) {
        $key = $this->getItemKey($item);
        unset($this->content[$key]);
        $this->validate();
    }

    public function updateItem(Item $item) {
        $key = $this->getItemKey($item);
        if (!isset($this->content[$key])) {
            throw new \Exception("Trying to update item that is not in the basket");
        }

        $this->content[$key] = $item;
        $this->validate();
    }

    /**
     * @return Item[]
     */
    public function getItems() {
        return $this->content;
    }

    public function getItemCount() {
        return count($this->content);
    }

    public function getAmountTotal() {
        $amount = 0;
        foreach ($this->content as $item) {
            $amount += $item->getAmount();
        }
        return $amount;
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->content as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    public function getTotalVat() {
        $total = 0;
        foreach ($this->content as $item) {
            $total += $item->getTotalVat();
        }
        return $total;
    }

    public function isEmpty() {
        return ($this->getItemCount() == 0);
    }

    public function clear() {
        $this->content = array();
    }

    /**
     * @param Item $item
     * @return string
     */
    private function getItemKey(Item $item)
    {
        $key = $item->getPlugin() . "-" . $item->getProductId() . "-" . $item->getPartnumber();
        return $key;
    }
} 