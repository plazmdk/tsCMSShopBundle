<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 5/19/14
 * Time: 9:59 PM
 */

namespace tsCMS\ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text',array(
                'label' => 'product.title',
                'required' => true,
                "attr" => array("class" => "productTitle")
            ))
            ->add("path", "route", array(
                "required" => false,
                "label"  => "product.path",
                "attr" => array(
                    "class" => "productPath route"
                )
            ))
            ->add('teaser', 'textarea',array(
                'label' => 'product.teaser',
                'required' => false
            ))
            ->add('partnumber', 'text',array(
                'label' => 'product.partnumber',
                'required' => true
            ))
            ->add('description', 'editor',array(
                'label' => 'product.description',
                'required' => false
            ))
            ->add('inventory', 'integer',array(
                'label' => 'product.inventory',
                'required' => false
            ))
            ->add('disabled', 'checkbox',array(
                'label' => 'product.disabled',
                'required' => false
            ))
            ->add('vatGroup', 'tscms_shop_vatgroup',array(
                'label' => 'product.vatGroup'
            ))
            ->add('price', 'tscms_shop_price', array(
                'label' => 'product.price',
            ))
            ->add('shipmentGroup', 'tscms_shop_shipmentgroup',array(
                'label' => 'product.shipmentGroup'
            ))
            ->add('weight', 'integer',array(
                'label' => 'product.weight',
                'required' => false
            ))
            ->add('images','tscms_filepicker_multiple',array(
                'label' => 'product.images',
                'class' => 'tsCMS\ShopBundle\Entity\Image',
                'required' => false,
                'image' => true,
                'titleField' => 'title',
                'descriptionField' => 'description',
                'positionField' => 'position'
            ))
            ->add('categories', 'tjb_treepicker_type', array(
                'class' => 'tsCMSShopBundle:Category',
                'label' => 'product.categories',
                'multiple' => true
            ))
            ->add("save","submit",array(
                'label' => 'product.save',
            ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\Product'
        ));
    }


    public function getName()
    {
        return "tsCMS_shop_producttype";
    }
}