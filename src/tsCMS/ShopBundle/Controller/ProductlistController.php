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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\Image;
use tsCMS\ShopBundle\Entity\Product;
use tsCMS\ShopBundle\Entity\Productlist;
use tsCMS\ShopBundle\Entity\ProductVariant;
use tsCMS\ShopBundle\Entity\Variant;
use tsCMS\ShopBundle\Entity\VariantOption;
use tsCMS\ShopBundle\Form\ProductlistType;
use tsCMS\ShopBundle\Form\ProductType;
use tsCMS\SystemBundle\Services\RouteService;

/**
 * @Route("/shop/productlist")
 */
class ProductlistController extends Controller {

    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $productlists = $this->getDoctrine()->getRepository("tsCMSShopBundle:Productlist")->findAll();

        return array(
            "productlists" => $productlists
        );
    }

    /**
     * @Route("/create")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Productlist:productlist.html.twig")
     */
    public function createAction(Request $request) {
        $productlist = new Productlist();

        $productlistForm = $this->createForm(new ProductlistType(), $productlist);
        $productlistForm->handleRequest($request);
        if ($productlistForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($productlist);
            $em->flush();

            $this->saveProductlistPath($productlist);

            return $this->redirect($this->generateUrl("tscms_shop_productlist_edit",array("id" => $productlist->getId())));
        }
        return array(
            "form" => $productlistForm->createView(),
            "productlist" => $productlist
        );
    }

    /**
     * @Route("/edit/{id}")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Productlist:productlist.html.twig")
     */
    public function editAction(Request $request, Productlist $productlist) {
        $productlistForm = $this->createForm(new ProductlistType(), $productlist);
        $productlistForm->handleRequest($request);
        if ($productlistForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->saveProductlistPath($productlist);

            return $this->redirect($this->generateUrl("tscms_shop_productlist_edit",array("id" => $productlist->getId())));
        }
        return array(
            "form" => $productlistForm->createView(),
            "productlist" => $productlist
        );
    }

    private function saveProductlistPath(Productlist $productlist) {
        /** @var RouteService $routeService */
        $routeService = $this->get("tsCMS.routeService");
        $name = $routeService->generateNameFromEntity($productlist);
        if ($productlist->getPath()) {
            $routeService->addRoute($name, $productlist->getTitle(), $productlist->getPath(),"tsCMSShopBundle:Shop:productlist","productlist",array("id" => $productlist->getId()),array(),false, true);
        } else {
            $routeService->removeRoute($name);
        }
    }

} 