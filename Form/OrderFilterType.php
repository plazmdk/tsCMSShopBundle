<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/19/14
 * Time: 7:00 PM
 */

namespace tsCMS\ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use tsCMS\ShopBundle\Model\Statuses;

class OrderFilterType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');
        $builder->add('status', 'filter_choice',array(
            'label' => 'order.status',
            'choices' => Statuses::$orderStatus,
            'multiple' => true,
            'expanded' => true
        ));
        $builder->add('paymentStatus', 'filter_choice', array(
            'label' => 'order.paymentStatus',
            'choices' => Statuses::$paymentStatus,
            'multiple' => true,
            'expanded' => true
        ));
        $builder->add('date', 'filter_date_range', array(
            'label' => 'order.date',
            'right_date_options' => array('widget' => 'single_text'),
            'left_date_options' => array('widget' => 'single_text')
        ));
        $builder->add('save', 'submit', array(
            'label' => 'order.filter'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "tscms_shopbundle_orderFilter";
    }
}