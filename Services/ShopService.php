<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/16/14
 * Time: 5:04 PM
 */

namespace tsCMS\ShopBundle\Services;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\Translator;
use tsCMS\ShopBundle\Entity\CustomerDetails;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\OrderLine;
use tsCMS\ShopBundle\Entity\PaymentMethod;
use tsCMS\ShopBundle\Entity\Productlist;
use tsCMS\ShopBundle\Model\Statuses;
use tsCMS\SystemBundle\Event\BuildSiteStructureEvent;
use tsCMS\SystemBundle\Model\SiteStructureAction;
use tsCMS\SystemBundle\Model\SiteStructureGroup;
use tsCMS\TemplateBundle\Event\GetTemplateTypesEvent;
use tsCMS\TemplateBundle\Model\TemplateType;

class ShopService {
    const ORDER_CONFIRMATION_TEMPLATE = "ORDER_CONFIRMATION";
    const ORDER_INVOICE_TEMPLATE = "INVOICE_CONFIRMATION";

    /** @var \Doctrine\ORM\EntityManager  */
    private $em;
    /** @var RouterInterface */
    private $router;
    /** @var Translator */
    private $translator;
    /** @var Session */
    private $session;
    /** @var \Symfony\Component\DependencyInjection\Container  */
    private $container;

    function __construct(EntityManager $em, RouterInterface $router, Translator $translator, Session $session, Container $container)
    {
        $this->em = $em;
        $this->router = $router;
        $this->translator = $translator;
        $this->session = $session;
        $this->container = $container;
    }


    public function onBuildSiteStructure(BuildSiteStructureEvent $event) {
        $pagesGroup = new SiteStructureGroup("Shop","fa-shopping-cart");
        $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("orders"),$this->router->generate("tscms_shop_order_index")));
        $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("products"),$this->router->generate("tscms_shop_product_index")));
        $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("productlists"),$this->router->generate("tscms_shop_productlist_index")));
        $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("categories"),$this->router->generate("tscms_shop_category_index")));
        if ($this->container->getParameter("variants")) {
            $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("variants"),$this->router->generate("tscms_shop_variant_index")));
        }
        $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("vatgroups"),$this->router->generate("tscms_shop_vatgroup_index")));
        $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("paymentmethods"),$this->router->generate("tscms_shop_payment_index")));
        $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("shipmentmethods"),$this->router->generate("tscms_shop_shipment_index")));
        $pagesGroup->addElement(new SiteStructureAction($this->translator->trans("config.title"),$this->router->generate("tscms_shop_configuration_index")));
        $event->addElement($pagesGroup);
    }

    /**
     * @param Productlist $productlist
     */
    public function getProductlistProducts(Productlist $productlist) {
        $qb = $this->em->createQueryBuilder();
        $qb->from("tsCMSShopBundle:Product","p");
        $qb->leftJoin("p.images","i");
        $qb->leftJoin("p.categories","c");
        $qb->select("p, i");


        if (count($productlist->getSingleProducts()) > 0) {
            $specificProducts = array();
            foreach ($productlist->getSingleProducts() as $specificProduct) {
                $specificProducts[] = $specificProduct->getProduct();
            }
            $qb->where("p IN (:specificProducts)");
            $qb->setParameter("specificProducts",$specificProducts);

        }

        if (count($productlist->getCategories()) > 0) {
            $qb->orWhere("c IN (:categories)");
            $qb->setParameter(":categories", $productlist->getCategories()->getValues());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param bool $new Forces the session to create a new order
     * @return Order
     */
    public function getOrder($new = false) {
        $order = $this->session->get("tsCMS_shop_order");
        if ($order === null || $new == true) {
            $order = new Order();
            $order->setDate(new \DateTime());
            $this->session->set("tsCMS_shop_order", $order);
        }
        return $order;
    }

    public function onGetTemplateTypesEvent(GetTemplateTypesEvent $event) {
        $customerDetails = new CustomerDetails();
        $customerDetails->setName("John Doe");
        $customerDetails->setAddress("co. Kim Doe");
        $customerDetails->setAddress2("Street 21");
        $customerDetails->setPostalcode("9000");
        $customerDetails->setCity("Angelesss");
        $customerDetails->setCountry("Denmark");
        $customerDetails->setEmail("tj@tjb.dk");
        $customerDetails->setPhone("24251545");
        $testOrder = new Order();
        $testOrder->setCustomerDetails($customerDetails);
        $testOrder->setShipmentDetails($customerDetails);
        $testOrder->setDate(new \DateTime());
        $testOrder->setNote("This is a note!");
        $paymentMethod = new PaymentMethod();
        $paymentMethod->setTitle("Creditcard");
        $testOrder->setPaymentMethod($paymentMethod);
        $testOrder->setPaymentStatus(Statuses::PAYMENT_CAPTURED);
        $testOrder->setStatus(Statuses::ORDER_RECEIVED);
        $line1 = new OrderLine();
        $line1->setTitle("Produkt of this");
        $line1->setAmount(2);
        $line1->setPartnumber("p1this");
        $line1->setPlugin("Shop");
        $line1->setPricePerUnit(12000);
        $line1->setVat(25);
        $line1->setProductId(1);
        $line2 = new OrderLine();
        $line2->setTitle("This other product");
        $line2->setAmount(2);
        $line2->setPartnumber("p2his");
        $line2->setPlugin("Shop");
        $line2->setPricePerUnit(4500);
        $line2->setVat(25);
        $line2->setProductId(1);
        $testOrder->setLines(array($line1, $line2));

        $orderConfirmationTemplate = new TemplateType(self::ORDER_CONFIRMATION_TEMPLATE, $this->translator->trans("order.confirmationTemplate"), array(
            "order" => $testOrder
        ));
        $orderInvoiceTemplate = new TemplateType(self::ORDER_INVOICE_TEMPLATE, $this->translator->trans("order.invoiceTemplate"), array(
            "order" => $testOrder
        ));

        $event->addTemplateType($orderConfirmationTemplate);
        $event->addTemplateType($orderInvoiceTemplate);
    }
} 