<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/1/14
 * Time: 5:16 PM
 */

namespace tsCMS\ShopBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VatGroupType extends AbstractType {
    /**
     * Tax category class name.
     *
     * @var string
     */
    protected $className;

    /**
     * Constructor.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'class' => $this->className,
                'option_attributes' => array('data-percentage' => 'percentage'),
                'attr' => array('class' => 'vatGroup')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tscms_shop_vatgroup';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'extended_entity';
    }
} 