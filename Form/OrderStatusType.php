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
    private $isCartOrder;

    function __construct($allowPaymentStatusChange, $isCartOrder)
    {
        $this->allowPaymentStatusChange = $allowPaymentStatusChange;
        $this->isCartOrder = $isCartOrder;
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
                'choices' => Statuses::$orderStatus,
                'required' => !$this->isCartOrder
            ))
            ->add('paymentStatus', 'choice', array(
                'label' => 'order.paymentStatus',
                'choices' => Statuses::$paymentStatus,
                'disabled' => $this->allowPaymentStatusChange,
                'required' => !$this->isCartOrder
            ));

        if ($this->isCartOrder) {
            $builder->add('cart', 'checkbox', array(
                'label' => 'order.isCart',
                'required' => false,
            ));
        } else {
            $builder->add('cart', 'hidden');
        }

        $builder
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