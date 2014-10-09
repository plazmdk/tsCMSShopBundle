<?php

namespace tsCMS\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use tsCMS\ShopBundle\Interfaces\TotalInterface;

/**
 * Order
 *
 * @ORM\Table(name="`order`")
 * @ORM\Entity
 */
class Order implements TotalInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cart", type="boolean")
     */
    private $cart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentStatus", type="string", length=255, nullable=true)
     */
    private $paymentStatus;

    /**
     * @var CustomerDetails
     *
     * @ORM\ManyToOne(targetEntity="CustomerDetails", cascade={"persist"})
     * @ORM\JoinColumn(name="customerDetails_id", referencedColumnName="id")
     */
    private $customerDetails;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean")
     */
    private $newsletter;

    /**
     * @var OrderLine[]
     *
     * @ORM\OneToMany(targetEntity="OrderLine", mappedBy="order", cascade={"persist"})
     */
    private $lines;

    /**
     * @var CustomerDetails
     *
     * @ORM\ManyToOne(targetEntity="CustomerDetails", cascade={"persist"})
     * @ORM\JoinColumn(name="shipmentDetail_id", referencedColumnName="id")
     */
    private $shipmentDetails;

    /**
     * @var PaymentMethod
     *
     * @ORM\ManyToOne(targetEntity="PaymentMethod")
     * @ORM\JoinColumn(name="paymentmethod_id", referencedColumnName="id")
     */
    private $paymentMethod;

    /**
     * @var mixed
     *
     * @ORM\Column(name="paymentFee", type="decimal")
     */
    private $paymentFee = 0;

    /**
     * @var PaymentTransaction[]
     *
     * @ORM\OneToMany(targetEntity="PaymentTransaction", mappedBy="order", cascade={"persist"})
     */
    private $paymentTransactions;

    public function __construct() {
        $this->lines = new ArrayCollection();
        $this->paymentTransactions = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param boolean $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return boolean
     */
    public function isCart()
    {
        return $this->cart;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Order
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $paymentStatus
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\CustomerDetails $customer
     */
    public function setCustomerDetails($customer)
    {
        $this->customerDetails = $customer;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\CustomerDetails
     */
    public function getCustomerDetails()
    {
        return $this->customerDetails;
    }

    /**
     * @param OrderLine $line
     */
    public function addLine(OrderLine $line) {
        $this->lines->add($line);
        $line->setOrder($this);
    }

    public function removeLine(OrderLine $line) {
        $this->lines->removeElement($line);
        $line->setOrder(null);
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\OrderLine[] $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\OrderLine[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\PaymentTransaction $paymentTransaction
     */
    public function addPaymentTransaction($paymentTransaction)
    {
        $this->paymentTransactions->add($paymentTransaction);
        $paymentTransaction->setOrder($this);
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\PaymentTransaction[]
     */
    public function getPaymentTransactions()
    {
        return $this->paymentTransactions;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param boolean $newsletter
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @return boolean
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\CustomerDetails $shipmentDetails
     */
    public function setShipmentDetails($shipmentDetails)
    {
        $this->shipmentDetails = $shipmentDetails;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\CustomerDetails
     */
    public function getShipmentDetails()
    {
        return $this->shipmentDetails;
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

    /**
     * @param mixed $paymentFee
     */
    public function setPaymentFee($paymentFee)
    {
        $this->paymentFee = $paymentFee;
    }

    /**
     * @return mixed
     */
    public function getPaymentFee()
    {
        return $this->paymentFee;
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->getLines() as $line) {
            $total += $line->getTotal();
        }

        $total += $this->getPaymentFee();

        return $total;
    }

    public function getTotalVat() {
        $total = 0;
        foreach ($this->getLines() as $line) {
            $total += $line->getTotalVat();
        }

        $total += $this->getPaymentFee();

        return $total;
    }
}
