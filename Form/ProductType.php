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
            ->add('productPrice', 'money',array(
                'label' => 'product.price',
                'divisor' => 100,
                'currency' => 'DKK',
                'required' => true,
                'attr' => array(
                    "class" => "priceCalc",
                    "data-price-group" => "price",
                    "data-price-vat" => "false"
                )
            ))
            ->add('productPriceVat', 'money',array(
                'label' => 'product.priceVat',
                'divisor' => 100,
                'currency' => 'DKK',
                'required' => false,
                'attr' => array(
                    "class" => "priceCalc",
                    "data-price-group" => "price",
                    "data-price-vat" => "true"
                )
            ))
            ->add('vatGroup', 'extended_entity',array(
                'label' => 'product.vatGroup',
                'class' => 'tsCMSShopBundle:VatGroup',
                'option_attributes' => array('data-percentage' => 'percentage'),
                'attr' => array(
                    "class" => "priceCalcVat",
                    "data-price-group" => "price",
                )
            ))
            ->add('images','filepicker',array(
                'label' => 'product.images',
                'class' => 'tsCMS\ShopBundle\Entity\Image',
                'required' => false,
                'image' => true,
                'titleField' => 'title',
                'descriptionField' => 'description'
            ))
            ->add('categories', 'entity', array(
                'class'         => 'tsCMSShopBundle:Category',
                'label'         => 'product.categories',
                'multiple'      => true
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