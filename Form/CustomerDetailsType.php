<?php

namespace tsCMS\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Orx;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use tsCMS\ShopBundle\Entity\Category;

class CustomerDetailsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array(
                'label' => 'customerDetails.name',
                'required' => true
            ))
            ->add('address','text', array(
                'label' => 'customerDetails.address',
                'required' => true
            ))
            ->add('address2','text', array(
                'label' => 'customerDetails.address2',
                'required' => false
            ))
            ->add('postalcode','text', array(
                'label' => 'customerDetails.postalcode',
                'required' => true
            ))
            ->add('city','text', array(
                'label' => 'customerDetails.city',
                'required' => true
            ))
            ->add('country','text', array(
                'label' => 'customerDetails.country',
                'required' => false
            ))
            ->add('email','email', array(
                'label' => 'customerDetails.email',
                'required' => true
            ))
            ->add('phone','text', array(
                'label' => 'customerDetails.phone',
                'required' => true
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'tsCMS\ShopBundle\Entity\CustomerDetails'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tscms_shopbundle_customerDetails';
    }
}
