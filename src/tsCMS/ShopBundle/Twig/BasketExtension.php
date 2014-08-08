<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/16/14
 * Time: 7:23 PM
 */

namespace tsCMS\ShopBundle\Twig;


use Symfony\Component\HttpFoundation\RequestStack;
use tsCMS\ShopBundle\Interfaces\PriceInterface;
use tsCMS\ShopBundle\Interfaces\TotalInterface;
use tsCMS\ShopBundle\Services\BasketService;

class BasketExtension extends \Twig_Extension {
    /** @var BasketService */
    private $basketService;

    function __construct(BasketService $basketService)
    {
        $this->basketService = $basketService;
    }

    public function getGlobals() {
        return array(
            "basket" => $this->basketService->getBasket()
        );
    }

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('currency', array($this, 'formatCurrency'))
        );
    }

    public function getBasketTotal() {

        return $this->basketService->getTotal();
    }

    public function formatCurrency($item,$forceTotal = false) {
        $price = 0;
        $priceVat = 0;
        if ($item instanceof PriceInterface && !$forceTotal) {
            $price = $item->getProductPrice();
            $priceVat = $item->getProductPriceVat();
        } else if ($item instanceof TotalInterface) {
            $price = $item->getTotal();
            $priceVat = $item->getTotalVat();
        }

        $showWithVat = true;

        if ($showWithVat) {
            $amount = $priceVat;
        } else {
            $amount = $price;
        }

        return number_format($amount / 100, 2, ",",".")." DKK";
    }

    public function getName()
    {
        return 'basket_extension';
    }
} 