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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\Image;
use tsCMS\ShopBundle\Entity\Product;
use tsCMS\ShopBundle\Entity\ProductVariant;
use tsCMS\ShopBundle\Entity\Variant;
use tsCMS\ShopBundle\Entity\VariantOption;
use tsCMS\ShopBundle\Form\ProductType;
use tsCMS\SystemBundle\Services\RouteService;

/**
 * @Route("/shop/product")
 */
class ProductController extends Controller {

    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $products = $this->getDoctrine()->getRepository("tsCMSShopBundle:Product")->findAll();

        return array(
            "products" => $products
        );
    }

    /**
     * @Route("/create")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Product:product.html.twig")
     */
    public function createAction(Request $request) {
        $product = new Product();

        $productForm = $this->createForm(new ProductType(), $product);
        $productForm->handleRequest($request);
        if ($productForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->saveProductRouteConfig($product);

            return $this->redirect($this->generateUrl("tscms_shop_product_edit",array("id" => $product->getId())));
        }
        return array(
            "form" => $productForm->createView(),
            "product" => $product
        );
    }

    /**
     * @Route("/edit/{id}")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Product:product.html.twig")
     */
    public function editAction(Request $request, Product $product) {
        $productForm = $this->createForm(new ProductType(), $product);
        $productForm->handleRequest($request);
        if ($productForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->saveProductRouteConfig($product);

            return $this->redirect($this->generateUrl("tscms_shop_product_edit",array("id" => $product->getId())));
        }
        return array(
            "form" => $productForm->createView(),
            "product" => $product
        );
    }

    /**
     * @Route("/{id}/listvariants")
     * @Secure("ROLE_ADMIN")
     * @Template
     */
    public function listVariantsAction(Product $product) {
        $variants = $this->getDoctrine()->getRepository("tsCMSShopBundle:Variant")->findAll();
        return array(
            "product" => $product,
            "variants" => $variants
        );
    }

    /**
     * @Route("/{id}/createvariants")
     * @Secure("ROLE_ADMIN")
     */
    public function createVariantsAction(Product $product, Request $request) {
        if (count($product->getVariants()) > 0) {
            // Please do not add several times...
            return $this->redirect($this->generateUrl("tscms_shop_product_edit",array("id" => $product->getId())));
        }
        $variantIds = $request->request->get("variant");
        $variantOptionIds = $request->request->get("variantoption");

        /** @var Variant[] $variants */
        $variants = $this->getDoctrine()->getRepository("tsCMSShopBundle:Variant")->findBy(array("id" => $variantIds));

        foreach ($variants as $variant) {
            $productVariant = new ProductVariant();
            $productVariant->setVariant($variant);
            $product->addVariant($productVariant);

            $enabledOptionIds = $variantOptionIds[$variant->getId()] ?: array();
            $enabledOptions = $this->getDoctrine()->getRepository("tsCMSShopBundle:VariantOption")->findBy(array("id" => $enabledOptionIds));
            foreach ($enabledOptions as $enabledOption) {
                $productVariant->addOption($enabledOption);
            }
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl("tscms_shop_product_edit",array("id" => $product->getId())));
    }

    private function saveProductRouteConfig(Product $product) {
        /** @var RouteService $routeService */
        $routeService = $this->get("tsCMS.routeService");
        $name = $routeService->generateNameFromEntity($product);
        if ($product->getRouteConfig()->getPath()) {
            $routeService->addRoute($name, $product->getRouteConfig()->getTitle() ? $product->getRouteConfig()->getTitle() : $product->getTitle(), $product->getRouteConfig()->getPath(),"tsCMSShopBundle:Shop:product","product",array("id" => $product->getId()),array(),false, true, $product->getRouteConfig()->getMetatags(), $product->getRouteConfig()->getMetadescription());
        } else {
            $routeService->removeRoute($name);
        }
    }


    private function enableVariantOption(Product $product, VariantOption $variantOption) {

    }

    /**
     * @Route("/autocomplete")
     * @Secure("ROLE_ADMIN")
     */
    public function autocompleteAction(Request $request) {
        $query = "%".$request->query->get("query", "")."%";
        /** @var Product[] $result */
        $result = $this->getDoctrine()->getEntityManager()->getRepository("tsCMSShopBundle:Product")->createQueryBuilder("p")->where("p.title LIKE :query")->getQuery()->setParameter("query", $query)->getResult();

        $data = array();
        foreach ($result as $row) {
            $data[] = array(
                "id" => $row->getId(),
                "title" => $row->getTitle(),
                "price" => $row->getPrice() / 100
            );
        }
        return new JsonResponse($data);
    }
} 