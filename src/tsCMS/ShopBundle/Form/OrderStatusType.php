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

class OrderStatusType extends AbstractType {
    private $allowPaymentStatusChange;

    function __construct($allowPaymentStatusChange)
    {
        $this->allowPaymentStatusChange = $allowPaymentStatusChange;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array(
                'label' => 'order.status',
                'choices' => Statuses::$orderStatus
            ))
            ->add('paymentStatus', 'choice', array(
                'label' => 'order.paymentStatus',
                'choices' => Statuses::$paymentStatus,
                'disabled' => $this->allowPaymentStatusChange
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