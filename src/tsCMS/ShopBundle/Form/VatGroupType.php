<?php

namespace tsCMS\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VatGroupType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text', array(
                'label' => 'vatgroup.title',
                'required' => true
            ))
            ->add('percentage', 'number', array(
                'precision' => 3,
                'label' => 'vatgroup.percentage',
                'required' => true
            ))
            ->add("save","submit",array(
                'label' => 'vatgroup.save',
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\VatGroup'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tscms_shopbundle_vatgroup';
    }
}
