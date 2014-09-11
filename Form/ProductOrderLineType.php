<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/5/14
 * Time: 8:06 PM
 */

namespace tsCMS\ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductOrderLineType extends OrderLineType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('product', 'infinite_form_entity_search', array(
                'class' => 'tsCMS\ShopBundle\Entity\Product',
                'search_route' => 'tscms_shop_product_autocomplete',
                'name' => 'title'
            ))
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\ProductOrderLine',
            'model_class' => 'tsCMS\ShopBundle\Entity\ProductOrderLine'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "tscms_shop_productorderline";
    }
}