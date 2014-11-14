<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 11/14/14
 * Time: 9:05 PM
 */

namespace tsCMS\ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SendEmailType extends AbstractType {

    private $action;

    public function __construct($action) {
        $this->action = $action;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add("email","email",array(
            "label" => "sendemail.email"
        ))->add("send","submit",array(
            "label" => "sendemail.send"
        ))
        ->setAction($this->action);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "tscms_shop_sendemail";
    }
}