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
use tsCMS\ShopBundle\Entity\Variant;
use tsCMS\ShopBundle\Entity\VatGroup;
use tsCMS\ShopBundle\Form\VariantType;
use tsCMS\ShopBundle\Form\VatGroupType;

/**
 * @Route("/shop/vatgroup")
 */
class VatGroupController extends Controller {

    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $vatGroups = $this->getDoctrine()->getRepository("tsCMSShopBundle:VatGroup")->findAll();

        return array(
            "vatgroups" => $vatGroups
        );
    }
    /**
     * @Route("/create")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:VatGroup:vatGroup.html.twig")
     */
    public function createAction(Request $request) {
        $vatGroup = new VatGroup();

        $vatGroupForm = $this->createForm(new VatGroupType(), $vatGroup);
        $vatGroupForm->handleRequest($request);
        if ($vatGroupForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($vatGroup);
            $em->flush();

            return $this->redirect($this->generateUrl("tscms_shop_vatgroup_edit",array("id" => $vatGroup->getId())));
        }
        return array(
            "form" => $vatGroupForm->createView()
        );
    }

    /**
     * @Route("/edit/{id}")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:VatGroup:vatGroup.html.twig")
     */
    public function editAction(Request $request, VatGroup $vatGroup) {
        $vatGroupForm = $this->createForm(new VatGroupType(), $vatGroup);
        $vatGroupForm->handleRequest($request);
        if ($vatGroupForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl("tscms_shop_vatgroup_edit",array("id" => $vatGroup->getId())));
        }
        return array(
            "form" => $vatGroupForm->createView()
        );
    }
} 