<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/13/14
 * Time: 9:51 PM
 */

namespace tsCMS\ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderShipmentType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shipmentDetails', new CustomerDetailsType(), array(
                'label' => 'order.shipmentDetails',
                'required' => false
            ))
            ->add('shipmentMethod', 'entity', array(
                'label' => 'order.shipmentMethod',
                'class' => 'tsCMSShopBundle:ShipmentMethod',
                'property' => 'title',
                'expanded' => true,
                'mapped' => false
            ))
            ->add("save","submit",array(
                'label' => 'order.next',
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
     * @return string
     */
    public function getName()
    {
        return 'tscms_shopbundle_orderShipment';
    }
} 