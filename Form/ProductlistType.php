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

class ProductlistType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text',array(
                'label' => 'productlist.title',
                'required' => true,
                "attr" => array("class" => "productlistTitle")
            ))
            ->add("content", "editor", array("required" => false, "label"  => "productlist.content"))
            ->add("path", "route", array(
                "required" => false,
                "label"  => "productlist.path",
                "attr" => array(
                    "class" => "productlistPath route"
                )
            ))
            ->add('singleProducts', 'collection', array(
                'label'          => 'productlist.singleProducts',
                'by_reference'   => false,
                'allow_add'      => true,
                'allow_delete'   => true,
                'type'           => new ProductlistProductType()
            ))
            ->add('categories', 'tjb_treepicker_type', array(
                'class' => 'tsCMSShopBundle:Category',
                'label' => 'productlist.categories',
                'multiple' => true
            ))
            ->add("pagination","number",array(
                'label' => 'productlist.pagination',
                'required' => false
            ))
            ->add("save","submit",array(
                'label' => 'productlist.save',
            ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\Productlist'
        ));
    }


    public function getName()
    {
        return "tsCMS_shop_productlisttype";
    }
}