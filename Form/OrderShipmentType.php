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
use tsCMS\ShopBundle\Services\ShipmentService;

class OrderShipmentType extends AbstractType {
    /** @var ShipmentService */
    private $shipmentService;
    /** @var  ShipmentMethod */
    private $selectedShipmentMethod;
    /** @var Order */
    private $order;

    public function __construct(ShipmentService $shipmentService, $selectedShipmentMethod, $order) {
        $this->shipmentService = $shipmentService;
        $this->selectedShipmentMethod = $selectedShipmentMethod;
        $this->order = $order;
    }

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
            ->add('shipmentMethod', 'extended_entity', array(
                'label' => 'order.shipmentMethod',
                'class' => 'tsCMSShopBundle:ShipmentMethod',
                'option_attributes' => array('data-allowdeliveryaddress' => 'deliveryAddressAllowed'),
                'choices' => $this->shipmentService->getPossibleShipmentMethods($this->order),
                'property' => 'title',
                'expanded' => true,
                'mapped' => false,
                'data' => $this->selectedShipmentMethod
            ))
            ->add("save","submit",array(
                'label' => 'order.next',
                'attr' => array(
                    'class' => 'btn btn-success'
                )
            ));


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