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

class VariantOptionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text',array(
                'label' => 'variantoption.title',
                'required' => true
            ))->add('displayTitle', 'text',array(
                'label' => 'variantoption.displayTitle',
                'required' => true
            ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\VariantOption'
        ));
    }


    public function getName()
    {
        return "tsCMS_shop_variantoptiontype";
    }
}