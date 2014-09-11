<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/20/14
 * Time: 4:14 PM
 */

namespace tsCMS\ShopBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\ShipmentGroup;
use tsCMS\ShopBundle\Entity\Variant;
use tsCMS\ShopBundle\Entity\VatGroup;
use tsCMS\ShopBundle\Form\ShipmentGroupType;
use tsCMS\ShopBundle\Form\VariantType;
use tsCMS\ShopBundle\Form\VatGroupType;

/**
 * @Route("/shop/shipmentgroup")
 */
class ShipmentGroupController extends Controller {

    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $shipmentGroups = $this->getDoctrine()->getRepository("tsCMSShopBundle:ShipmentGroup")->findAll();

        return array(
            "shipmentgroups" => $shipmentGroups
        );
    }
    /**
     * @Route("/create")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:ShipmentGroup:shipmentGroup.html.twig")
     */
    public function createAction(Request $request) {
        $shipmentGroup = new ShipmentGroup();

        $shipmentGroupForm = $this->createForm(new ShipmentGroupType(), $shipmentGroup);
        $shipmentGroupForm->handleRequest($request);
        if ($shipmentGroupForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($shipmentGroup);
            $em->flush();

            return $this->redirect($this->generateUrl("tscms_shop_shipmentgroup_edit",array("id" => $shipmentGroup->getId())));
        }
        return array(
            "form" => $shipmentGroupForm->createView()
        );
    }

    /**
     * @Route("/edit/{id}")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:ShipmentGroup:shipmentGroup.html.twig")
     */
    public function editAction(Request $request, ShipmentGroup $shipmentGroup) {
        $shipmentGroupForm = $this->createForm(new ShipmentGroupType(), $shipmentGroup);
        $shipmentGroupForm->handleRequest($request);
        if ($shipmentGroupForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl("tscms_shop_shipmentgroup_edit",array("id" => $shipmentGroup->getId())));
        }
        return array(
            "form" => $shipmentGroupForm->createView()
        );
    }
} 