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
    private $addressOnly;

    function __construct($addressOnly = false)
    {
        $this->addressOnly = $addressOnly;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$this->addressOnly) {
            $builder->add('name','text', array(
                'label' => 'customerDetails.name',
                'required' => true
            ));

        }

        $builder->add('address','text', array(
            'label' => 'customerDetails.address',
            'required' => true
        ));
        $builder->add('address2','text', array(
            'label' => 'customerDetails.address2',
            'required' => false
        ));
        $builder->add('postalcode','text', array(
            'label' => 'customerDetails.postalcode',
            'required' => true
        ));
        $builder->add('city','text', array(
            'label' => 'customerDetails.city',
            'required' => true
        ));
        $builder->add('country','text', array(
            'label' => 'customerDetails.country',
            'required' => false
        ));
        if (!$this->addressOnly) {
            $builder->add('email','email', array(
                'label' => 'customerDetails.email',
                'required' => true
            ));
            $builder->add('phone','text', array(
                'label' => 'customerDetails.phone',
                'required' => true
            ));
        }
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
