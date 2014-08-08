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
use tsCMS\ShopBundle\Form\VariantType;

/**
 * @Route("/shop/variant")
 */
class VariantController extends Controller {

    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $variants = $this->getDoctrine()->getRepository("tsCMSShopBundle:Variant")->findAll();

        return array(
            "variants" => $variants
        );
    }
    /**
     * @Route("/create")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Variant:variant.html.twig")
     */
    public function createAction(Request $request) {
        $variant = new Variant();

        $variantForm = $this->createForm(new VariantType(), $variant);
        $variantForm->handleRequest($request);
        if ($variantForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($variant);
            $em->flush();

            return $this->redirect($this->generateUrl("tscms_shop_variant_edit",array("id" => $variant->getId())));
        }
        return array(
            "form" => $variantForm->createView()
        );
    }

    /**
     * @Route("/edit/{id}")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Variant:variant.html.twig")
     */
    public function editAction(Request $request, Variant $variant) {
        $variantForm = $this->createForm(new VariantType(), $variant);
        $variantForm->handleRequest($request);
        if ($variantForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl("tscms_shop_variant_edit",array("id" => $variant->getId())));
        }
        return array(
            "form" => $variantForm->createView()
        );
    }
} 