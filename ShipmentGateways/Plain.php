<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 8/2/14
 * Time: 3:59 PM
 */

namespace tsCMS\ShopBundle\ShipmentGateways;


use BCA\CURL\CURL;
use Symfony\Component\Form\FormBuilderInterface;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Interfaces\ShipmentGatewayInterface;
use tsCMS\ShopBundle\ShipmentGateways\PlainGateway\DistancePriceType;
use tsCMS\ShopBundle\ShipmentGateways\PlainGateway\PostalcodePriceType;

class Plain extends ShipmentGatewayInterface {

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
            ->add('allowDeliveryAddress', 'checkbox',array(
                'label' => 'shipmentgateway.plain.allowdeliveryaddress',
                'required' => false
            ))
            ->add('price', 'tscms_shop_price',array(
                'label' => 'shipmentgateway.plain.standardprice'
            ))
            ->add('distanceAddress', 'text', array(
                'label' => 'shipmentgateway.plain.distanceaddress',
                'required' => false
            ))
            ->add('distancePrices','collection', array(
                'label'          => 'shipmentgateway.plain.distanceprices',
                'by_reference'   => false,
                'allow_add'      => true,
                'allow_delete'   => true,
                'type'           => new DistancePriceType()
            ))
            ->add('postalcodePrices','collection', array(
                'label'          => 'shipmentgateway.plain.postalcodeprices',
                'by_reference'   => false,
                'allow_add'      => true,
                'allow_delete'   => true,
                'type'           => new PostalcodePriceType()
            ));
    }

    public function calculatePrice(Order $order)
    {
        $possiblePrices = array($this->options['price']);

        $details = $order->getCustomerDetails();
        if ($order->getShipmentDetails()) {
            $details = $order->getShipmentDetails();
        }

        $amount = $this->getTotalAmountMatchingFreightGroup($order);

        if ($this->options['distanceAddress']
            && is_array($this->options['distancePrices'])
            && count($this->options['distancePrices']) > 0) {

            $address = $details->getAddress().", ".$details->getPostalcode()." ".$details->getCity().", ".$details->getCountry();

            $params = array(
                'origin'        => $this->options['distanceAddress'],
                'destination'   => $address,
                'sensor'        => 'true',
            );

            try {
                // Request URL
                $url = "http://maps.googleapis.com/maps/api/directions/json";

                $curl = new CURL($url);
                $curl->params($params);

                $result = $curl->get();
                if ($result->status() == 200) {
                    $result = json_decode($result);

                    $distance = $result->routes[0]->legs[0]->distance->value / 1000;

                    foreach ($this->options['distancePrices'] as $distancePrice) {
                        if ($distance <= $distancePrice['distance']) {
                            $possiblePrices[] = $distancePrice['price'];
                        }
                    }
                }
            } catch (\Exception $e) {

            }
        }

        if (is_array($this->options['postalcodePrices'])) {
            foreach ($this->options['postalcodePrices'] as $postalcodePrice) {
                if ($postalcodePrice['fromPostalcode'] <= $details->getPostalcode()
                    && $postalcodePrice['toPostalcode'] >= $details->getPostalcode()
                    && (empty($postalcodePrice['amount']) || $postalcodePrice['amount'] <= $amount)) {
                        $possiblePrices[] = $postalcodePrice['price'] * $amount;
                }
            }
        }

        sort($possiblePrices);
        return $possiblePrices[0];
    }

    public function getOptionFormTemplate()
    {
        return "tsCMSShopBundle:Shipment/Gateway:plain.html.twig";
    }

    /**
     * Specifies wherever the method
     * @return boolean
     */
    public function allowDeliveryAddress()
    {
        return $this->options['allowDeliveryAddress'];
    }
}