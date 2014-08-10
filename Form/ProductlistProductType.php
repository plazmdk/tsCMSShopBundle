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

class ProductlistProductType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("product", "entity", array(
                'class' => 'tsCMSShopBundle:Product',
                "required" => true,
                "label"  => "productlist.product",
                "attr" => array(
                    "class" => "productlistPath route"
                )
            ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\ProductlistProduct'
        ));
    }


    public function getName()
    {
        return "tsCMS_shop_productlistproducttype";
    }
}