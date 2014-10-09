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

class OrderDetailsType extends AbstractType {
    /** @var Boolean */
    private $hasNewsletter;

    function __construct($hasNewsletter)
    {
        $this->hasNewsletter = $hasNewsletter;
    }

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
            ->add('note', 'textarea', array(
                'label' => 'order.note',
                'required' => false,
                'attr' => array(
                    'rows' => 5
                )
            ));
        if ($this->hasNewsletter) {
            $builder
            ->add('newsletter', 'checkbox', array(
                'label' => 'order.newsletter',
                'required' => false
            ));
        }
        $builder
            ->add("save","submit",array(
                'label' => 'order.next',
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