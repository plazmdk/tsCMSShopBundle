<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/23/14
 * Time: 8:52 PM
 */

namespace tsCMS\ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use tsCMS\ShopBundle\Model\Statuses;

class OrderCustomerDetailsType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerDetails', new CustomerDetailsType(), array(
                'label' => 'order.customerDetails',
                'required' => true
            ))
            ->add('shipmentDetails', new CustomerDetailsType(), array(
                'label' => 'order.shipmentDetails',
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
     * @return string
     */
    public function getName()
    {
        return 'tscms_shopbundle_orderDetails';
    }
} 