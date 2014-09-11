<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 9/1/14
 * Time: 6:36 PM
 */

namespace tsCMS\ShopBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PriceType extends AbstractType {


    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'divisor' => 100,
                'currency' => 'DKK',
                'required' => true,
                'showHelper' => true
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['helper'] = $options['showHelper'];
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "tscms_shop_price";
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'money';
    }
}