<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/16/14
 * Time: 7:23 PM
 */

namespace tsCMS\ShopBundle\Twig;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use tsCMS\ShopBundle\Interfaces\PriceInterface;
use tsCMS\ShopBundle\Interfaces\TotalInterface;
use tsCMS\ShopBundle\Services\BasketService;

class ShopExtension extends \Twig_Extension {
    /** @var Session */
    private $session;

    function __construct($session)
    {
        $this->session = $session;
    }


    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('currency', array($this, 'currency'))
        );
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('tscms_shop_pricecalc', array($this, 'priceCalc')),
            new \Twig_SimpleFunction('tscms_shop_totalcalc', array($this, 'totalCalc'))
        );
    }

    public function getName()
    {
        return 'shop_extension';
    }

    public function priceCalc($item)
    {
        if ($item instanceof PriceInterface) {
            if ($this->session->get("tscms_shop_no_vat", false)) {
                return $item->getPrice();
            } else {
                return $item->getPrice() * (100 + $item->getVatGroup()->getPercentage()) / 100;
            }
        }
        throw new \Exception("Object does not extend PriceInterface");
    }

    public function totalCalc($item)
    {
        if ($item instanceof TotalInterface) {
            if ($this->session->get("tscms_shop_no_vat", false)) {
                return $item->getTotal();
            } else {
                return $item->getTotalVat();
            }
        }
        throw new \Exception("Object does not extend TotalInterface");
    }

    public function currency($amount)
    {
        return number_format($amount / 100, 2, ",",".")." DKK";
    }
} 