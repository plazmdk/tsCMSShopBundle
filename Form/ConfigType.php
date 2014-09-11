<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 6/2/14
 * Time: 10:16 PM
 */

namespace tsCMS\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use tsCMS\ShopBundle\Services\ShipmentService;
use tsCMS\ShopBundle\Services\ShopService;

class ConfigType extends AbstractType {

    /** @var  ShipmentService */
    private $shipmentService;

    function __construct($shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("productUrl","text", array(
                "label" => "config.productUrl",
            ))
            ->add("basketUrl","text", array(
                "label" => "config.basketUrl",
            ))
            ->add("checkoutUrl","text", array(
                "label" => "config.checkoutUrl",
            ))
            ->add("selectShipmentUrl","text", array(
                "label" => "config.selectShipmentUrl",
            ))
            ->add("selectPaymentUrl","text", array(
                "label" => "config.selectPaymentUrl",
            ))
            ->add("confirmOrderUrl","text", array(
                "label" => "config.confirmOrderUrl",
            ))
            ->add("approvedPaymentUrl","text", array(
                "label" => "config.approvedPaymentUrl",
            ))
            ->add("failedPaymentUrl","text", array(
                "label" => "config.failedPaymentUrl",
            ))
            ->add("paymentCallbackUrl","text", array(
                "label" => "config.paymentCallbackUrl",
            ))
            ->add("paymentCallbackUrl","text", array(
                "label" => "config.paymentCallbackUrl",
            ))
            ->add("openCartUrl","text", array(
                "label" => "config.openCartUrl",
            ))
            ->add("orderConfirmationTemplate","entity", array(
                "label" => "config.orderConfirmationTemplate",
                "class" => "tsCMSTemplateBundle:Template",
                'query_builder' => function(EntityRepository $er)
                {
                    $qb = $er->createQueryBuilder('t');
                    $qb->where("t.type = :type");
                    $qb->setParameter("type", ShopService::ORDER_CONFIRMATION_TEMPLATE);

                    return $qb;
                },
                'property' => 'title'
            ))
            ->add("orderInvoiceTemplate","entity", array(
                "label" => "config.orderInvoiceTemplate",
                "class" => "tsCMSTemplateBundle:Template",
                'query_builder' => function(EntityRepository $er)
                {
                    $qb = $er->createQueryBuilder('t');
                    $qb->where("t.type = :type");
                    $qb->setParameter("type", ShopService::ORDER_INVOICE_TEMPLATE);

                    return $qb;
                },
                'property' => 'title'
            ))
            ->add("shopName","text", array(
                "label" => "config.shopName"
            ))
            ->add("shopEmail","email", array(
                "label" => "config.shopEmail"
            ))
            ->add("shipmentRequireMatch","choice", array(
                "label" => "config.shipmentRequireMatch",
                "choices" => array(1,0)
            ))
            ->add("shipmentRequireMatch","choice", array(
                "label" => "config.shipmentRequireMatch",
                "choices" => array(1,0)
            ))
            ->add("shipmentFallbackMethod","entity", array(
                "label" => "config.shipmentFallbackMethod",
                "class" => "tsCMSShopBundle:ShipmentMethod",
                "choices" => $this->shipmentService->getEnabledShipmentMethods(),
                "required" => false,
                "property" => "title"
            ))
            ->add("save","submit", array(
                "label" => "config.save"
            ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Model\Config'
        ));
    }


    public function getName()
    {
        return "tsCMS_shop_configtype";
    }
} 