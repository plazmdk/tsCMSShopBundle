<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/5/14
 * Time: 8:33 PM
 */

namespace tsCMS\ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class OrderLineType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_type', 'hidden', array(
            'data'   => $this->getName(),
            'mapped' => false
        ));
        $builder->add('amount','number', array(
            'attr' => array('class' => 'amount')
        ));
        $builder->add('price','tscms_shop_price', array(
            'showHelper' => false
        ));
        $builder->add('fixedPrice','checkbox',array(
            'label' => ' ',
            'required' => false
        ));
        $builder->add('total', 'tscms_shop_price', array(
            'attr' => array('class' => 'total'),
            'showHelper' => false,
            'mapped' => false
        ));
    }
} 