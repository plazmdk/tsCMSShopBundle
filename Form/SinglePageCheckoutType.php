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
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\ShipmentMethod;
use tsCMS\ShopBundle\Services\PaymentService;
use tsCMS\ShopBundle\Services\ShipmentService;

class SinglePageCheckoutType extends AbstractType {
    /** @var ShipmentService */
    private $shipmentService;
    /** @var  ShipmentMethod */
    private $selectedShipmentMethod;
    /** @var PaymentService */
    private $paymentService;
    /** @var Order */
    private $order;
    /** @var Boolean */
    private $hasNewsletter;

    public function __construct(ShipmentService $shipmentService, $selectedShipmentMethod, PaymentService $paymentService, $order, $hasNewsletter = false) {
        $this->shipmentService = $shipmentService;
        $this->selectedShipmentMethod = $selectedShipmentMethod;
        $this->paymentService = $paymentService;
        $this->order = $order;
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
            ->add('shipmentDetails', new CustomerDetailsType(true), array(
                'label' => 'order.shipmentDetails',
                'required' => false
            ))
            ->add('note', 'textarea', array(
                'label' => 'order.note',
                'required' => false,
                'attr' => array(
                    'rows' => 5
                )
            ));
        if ($this->hasNewsletter) {
            $builder->add('newsletter', 'checkbox', array(
                'label' => 'order.newsletter',
                'required' => false
            ));
        }
        $builder
            ->add('shipmentMethod', 'extended_entity', array(
                'label' => 'order.shipmentMethod',
                'class' => 'tsCMSShopBundle:ShipmentMethod',
                'option_attributes' => array('data-allowdeliveryaddress' => 'deliveryAddressAllowed'),
                'option_details' => array("description" => "description"),
                'choices' => $this->shipmentService->getPossibleShipmentMethods($this->order),
                'property' => 'title',
                'expanded' => true,
                'mapped' => false,
                'data' => $this->selectedShipmentMethod
            ))
            ->add('paymentMethod', 'entity', array(
                'label' => 'order.paymentMethod',
                'class' => 'tsCMSShopBundle:PaymentMethod',
                'choices' => $this->paymentService->getEnabledPaymentMethods(),
                'property' => 'title',
                'expanded' => true,
            ))
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