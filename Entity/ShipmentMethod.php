<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 5/19/14
 * Time: 9:08 PM
 */

namespace tsCMS\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="shipmentmethod")
 * @ORM\Entity
 */
class ShipmentMethod {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;
    /**
     * @ORM\Column(type="string")
     */
    protected $gateway;
    /**
     * @ORM\Column(type="json_array")
     */
    protected $options;
    /**
     * @ORM\Column(type="integer")
     */
    protected $position;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;
    /**
     * @var VatGroup
     *
     * @ORM\ManyToOne(targetEntity="VatGroup")
     * @ORM\JoinColumn(name="vatGroup_id", referencedColumnName="id")
     */
    protected $vatGroup;
    /**
     * @var ShipmentGroup[]
     *
     * @ORM\ManyToMany(targetEntity="ShipmentGroup")
     * @ORM\JoinTable(name="shipmentmethod_shipmentgroup",
     *      joinColumns={@ORM\JoinColumn(name="shipmentmethod_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="shipmentgroup_id", referencedColumnName="id")}
     *      )
     */
    protected $shipmentGroups;

    function __construct()
    {
        $this->shipmentGroups = new ArrayCollection();
    }


    /**
     * @param mixed $config
     */
    public function setOptions($config)
    {
        $this->options = $config;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return mixed
     */
    public function getGateway()
    {
        return $this->gateway;
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
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param \tsCMS\ShopBundle\Entity\VatGroup $vatGroup
     */
    public function setVatGroup($vatGroup)
    {
        $this->vatGroup = $vatGroup;
    }

    /**
     * @return \tsCMS\ShopBundle\Entity\VatGroup
     */
    public function getVatGroup()
    {
        return $this->vatGroup;
    }

    /**
     * @param mixed $shipmentGroups
     */
    public function setShipmentGroups($shipmentGroups)
    {
        $this->shipmentGroups = $shipmentGroups;
    }

    /**
     * @return ShipmentGroup[]
     */
    public function getShipmentGroups()
    {
        return $this->shipmentGroups;
    }



    /*************
     * Unmapped
     *************/

    private $deliveryAddressAllowed;

    /**
     * @param mixed $deliveryAddressAllowed
     */
    public function setDeliveryAddressAllowed($deliveryAddressAllowed)
    {
        $this->deliveryAddressAllowed = $deliveryAddressAllowed;
    }

    /**
     * @return mixed
     */
    public function getDeliveryAddressAllowed()
    {
        return $this->deliveryAddressAllowed;
    }


} 