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
use tsCMS\ShopBundle\Entity\Category;
use tsCMS\ShopBundle\Form\CategoryType;

/**
 * @Route("/shop/category")
 */
class CategoryController extends Controller {

    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction() {
        $categories = $this->getDoctrine()->getRepository("tsCMSShopBundle:Category")->findAll();

        return array(
            "categories" => $categories
        );
    }
    /**
     * @Route("/create")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Category:category.html.twig")
     */
    public function createAction(Request $request) {
        $category = new Category();

        $categoryForm = $this->createForm(new CategoryType(), $category);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirect($this->generateUrl("tscms_shop_category_edit",array("id" => $category->getId())));
        }
        return array(
            "form" => $categoryForm->createView()
        );
    }

    /**
     * @Route("/edit/{id}")
     * @Secure("ROLE_ADMIN")
     * @Template("tsCMSShopBundle:Category:category.html.twig")
     */
    public function editAction(Request $request, Category $variant) {
        $categoryForm = $this->createForm(new CategoryType(), $variant);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl("tscms_shop_category_edit",array("id" => $variant->getId())));
        }
        return array(
            "form" => $categoryForm->createView()
        );
    }
} 