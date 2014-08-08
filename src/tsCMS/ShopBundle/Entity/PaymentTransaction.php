<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/23/14
 * Time: 2:12 PM
 */

namespace tsCMS\ShopBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
/**
 * PaymentTransaction
 *
 * @ORM\Table(name="paymenttransaction")
 * @ORM\Entity
 */
class PaymentTransaction {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="paymentTransactions")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;
    /**
     * @var PaymentMethod
     *
     * @ORM\ManyToOne(targetEntity="PaymentMethod")
     * @ORM\JoinColumn(name="paymentmethod_id", referencedColumnName="id")
     */
    private $paymentMethod;
    /**
     * @ORM\Column(type="string")
     */
    protected $type;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $captured;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $transactionId;

    /**
     * @param mixed $captured
     */
    public function setCaptured($captured)
    {
        $this->captured = $captured;
    }

    /**
     * @return mixed
     */
    public function getCaptured()
    {
        return $this->captured;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\PaymentMethod $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }


} 