<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/2/14
 * Time: 9:26 PM
 */

namespace tsCMS\ShopBundle\ShipmentGateways\PlainGateway;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PostalcodePriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', 'tscms_shop_price')
            ->add('amount', 'number')
            ->add('fromPostalcode', 'number', array(
                'label' => 'shipmentgateway.plain.frompostalcode'
            ))
            ->add('toPostalcode', 'number', array(
                'label' => 'shipmentgateway.plain.topostalcode'
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "tscms_shop_shipmentgateway_plain_postalcodeprice";
    }
}