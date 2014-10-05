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
        $basketService = $this->basketService;
        return array(
            "miniBasket" => array(
                "order" => $basketService->getOrder(),
                "itemCount" => $basketService->getItemCount(),
                "amountTotal" => $basketService->getAmountTotal(),
                "total" => $basketService->getTotal(),
                "totalVat" => $basketService->getTotalVat()
            )
        );
    }

    public function getBasketTotal() {

        return $this->basketService->getTotal();
    }

    public function getName()
    {
        return 'basket_extension';
    }
} 