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

class VariantType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text',array(
                'label' => 'variant.title',
                'required' => true
            ))->add('displayTitle', 'text',array(
                'label' => 'variant.displayTitle',
                'required' => true
            ))
            ->add('options', 'collection', array(
                'label'          => 'variant.options',
                'by_reference'   => false,
                'allow_add'      => true,
                'allow_delete'   => true,
                'type'           => new VariantOptionType()
            ))
            ->add("save","submit",array(
                'label' => 'variant.save',
            ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\Variant'
        ));
    }


    public function getName()
    {
        return "tsCMS_shop_varianttype";
    }
}