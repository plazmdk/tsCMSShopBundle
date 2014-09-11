<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/4/14
 * Time: 5:57 PM
 */

namespace tsCMS\ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use tsCMS\ShopBundle\Model\Statuses;

class CreateOrderType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array(
                'label' => 'order.status',
                'choices' => Statuses::$orderStatus,
                'required' => false
            ))
            ->add('paymentStatus', 'choice', array(
                'label' => 'order.paymentStatus',
                'choices' => Statuses::$paymentStatus,
                'required' => false
            ))
            ->add('cart', 'checkbox', array(
                'label' => 'order.isCart',
                'required' => false,
            ))
            ->add('customerDetails', new CustomerDetailsType(), array(
                'label' => 'order.customerDetails',
                'required' => false
            ))
            ->add('shipmentDetails', new CustomerDetailsType(), array(
                'label' => 'order.shipmentDetails',
                'required' => false
            ))
            ->add('lines', 'infinite_form_polycollection',array(
                'types' => array(
                    'tscms_shop_productorderline',
                    'tscms_shop_shipmentorderline'
                ),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('note', 'textarea', array(
                'label' => 'order.note',
                'required' => false
            ))
            ->add("save","submit",array(
                'label' => 'order.save',
            ));

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\Order'
        ));
    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "tscms_shop_order_createtype";
    }
}