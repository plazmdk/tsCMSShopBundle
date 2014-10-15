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
            ->add("singlePageCheckout", "choice", array(
                "label" => "config.singlePageCheckout.name",
                "choices" => array(
                    0 => "config.singlePageCheckout.no",
                    1 => "config.singlePageCheckout.yes"
                )
            ))
            ->add("productUrl","text", array(
                "label" => "config.productUrl",
            ))
            ->add("basketUrl","route", array(
                "label" => "config.basketUrl",
            ))
            ->add("checkoutUrl","route", array(
                "label" => "config.checkoutUrl",
            ))
            ->add("selectShipmentUrl","route", array(
                "label" => "config.selectShipmentUrl",
            ))
            ->add("selectPaymentUrl","route", array(
                "label" => "config.selectPaymentUrl",
            ))
            ->add("confirmOrderUrl","route", array(
                "label" => "config.confirmOrderUrl",
            ))
            ->add("approvedPaymentUrl","route", array(
                "label" => "config.approvedPaymentUrl",
            ))
            ->add("failedPaymentUrl","route", array(
                "label" => "config.failedPaymentUrl",
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
            ->add("sendConfirmationToAdmin", "checkbox", array(
                "label" => "config.sendConfirmationToAdmin",
                "required" => false
            ))
            ->add("sendInvoiceToAdmin", "checkbox", array(
                "label" => "config.sendInvoiceToAdmin",
                "required" => false
            ))
            ->add("shopName","text", array(
                "label" => "config.shopName"
            ))
            ->add("shopEmail","email", array(
                "label" => "config.shopEmail"
            ))
            ->add("newsletter","entity", array(
                "label" => "config.newsletter",
                "class" => "tsCMSNewsletterBundle:NewsletterList",
                'property' => 'title',
                'required' => false
            ))
            ->add("termsPage","entity", array(
                "label" => "config.terms",
                "class" => "tsCMSSystemBundle:Route",
                'query_builder' => function(EntityRepository $er)
                    {
                        $qb = $er->createQueryBuilder('r');
                        $qb->where("r.bundle != 'product' AND r.bundle != 'shop'");

                        return $qb;
                    },
                'property' => 'title',
                'required' => false
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