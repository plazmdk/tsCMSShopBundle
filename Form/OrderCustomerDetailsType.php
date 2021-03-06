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
            ->add('status', 'hidden')
            ->add('paymentStatus', 'hidden')
            ->add('paymentFee', 'hidden')
            ->add('customerDetails', new CustomerDetailsType(), array(
                'label' => 'order.customerDetails',
                'required' => true
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
                'attr' => array(
                    'class' => 'btn btn-success'
                )
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