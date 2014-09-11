<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 4/20/14
 * Time: 4:14 PM
 */

namespace tsCMS\ShopBundle\Controller;

use Doctrine\ORM\EntityManager;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\ShipmentMethod;
use tsCMS\ShopBundle\Entity\Variant;
use tsCMS\ShopBundle\Entity\VatGroup;
use tsCMS\ShopBundle\Form\VariantType;
use tsCMS\ShopBundle\Form\VatGroupType;
use tsCMS\ShopBundle\Interfaces\ShipmentGatewayInterface;
use tsCMS\ShopBundle\Services\ShipmentService;

/**
 * @Route("/shop/shipment")
 */
class ShipmentController extends Controller {

    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        /** @var ShipmentService $shipmentService */
        $shipmentService = $this->get("tsCMS_shop.shipmentservice");

        /** @var ShipmentMethod[] $shipmentMethods */
        $shipmentMethods = $shipmentService->getShipmentMethods();

        if ($request->isMethod("POST")) {
            $methods = $request->request->get("form",array());

            $formBuilder = $this->createFormBuilder();
            $data = array();

            $found = array();

            foreach ($methods as $index => $method) {
                if (!is_array($method)) {
                    continue;
                }

                $shipmentMethod = null;
                if ($method['id']) {
                    $found[] = $method['id'];

                    foreach ($shipmentMethods as $aShipmentMethod) {
                        if ($aShipmentMethod->getId() == $method['id']) {
                            $shipmentMethod = $aShipmentMethod;
                            break;
                        }
                    }
                }
                if (!$shipmentMethod) {
                    $shipmentMethod = new ShipmentMethod();
                    $this->getDoctrine()->getManager()->persist($shipmentMethod);
                }

                $data[$index] = $shipmentMethod;
                $gateway = $shipmentService->getShipmentGateway($method['gateway']);
                $this->getGatewayOptionForm($gateway, $index, $shipmentMethod, $formBuilder);

            }

            $form = $formBuilder->getForm();
            $form->setData($data);
            $form->handleRequest($request);

            if ($form->isValid()) {
                foreach ($shipmentMethods as $shipmentMethod) {
                    if (!in_array($shipmentMethod->getId(), $found)) {
                        $shipmentMethod->setDeleted(true);
                    }
                }

                $this->getDoctrine()->getManager()->flush();
            }

            return $this->redirect($this->generateUrl("tscms_shop_shipment_index"));
        }


        $formBuilder = $this->createFormBuilder();

        $populatedshipmentmethods = array();
        foreach ($shipmentMethods as $index => $shipmentMethod) {
            $gateway = $shipmentService->getShipmentGateway($shipmentMethod);

            $populatedshipmentmethods[] = array(
                "method" => $shipmentMethod,
                "gateway" => $gateway,
                "template" => $gateway->getOptionFormTemplate()
            );
            $this->getGatewayOptionForm($gateway, $index, $shipmentMethod, $formBuilder);
        }

        return array(
            "shipmentForm" => $formBuilder->getForm()->createView(),
            "shipmentMethods" => $populatedshipmentmethods,
            "availableGateways" => $this->getAvailableGateways()
        );
    }

    /**
     * @return ShipmentGatewayInterface[]
     */
    private function getAvailableGateways() {
        /** @var ShipmentService $shipmentService */
        $shipmentService = $this->get("tsCMS_shop.shipmentservice");

        $finder = new Finder();
        $gateways = array();
        foreach ($finder->files()->depth(0)->in(__DIR__."/../ShipmentGateways") as $file) { /** @var $file SplFileInfo */
            $gateway = $shipmentService->getShipmentGateway($file->getBasename(".".$file->getExtension()));

            $shipmentMethod = new ShipmentMethod();
            $shipmentMethod->setGateway($file->getBasename(".".$file->getExtension()));
            $gateways[] = array(
                'gateway' => $gateway,
                'optionForm' => $this->getGatewayOptionForm($gateway,null,$shipmentMethod)->getForm()->createView(),
                'template' => $gateway->getOptionFormTemplate()
            );
        }

        return $gateways;
    }

    /**
     * @param ShipmentGatewayInterface $gateway
     * @return \Symfony\Component\Form\FormBuilder
     */
    private function getGatewayOptionForm(ShipmentGatewayInterface $gateway, $index, ShipmentMethod $shipmentmethod, $wrap = null) {
        if (!$wrap) {
            $wrap = $this->createFormBuilder();
        }

        $prefix = $index !== null ? $index : "_P_";

        $formBuilder = $wrap->create($prefix, null, array(
            "compound" => true,
            "data_class" => "tsCMS\\ShopBundle\\Entity\\ShipmentMethod"
        ));
        $formBuilder->setData($shipmentmethod);
        $formBuilder->add("id", "hidden", array("required" => false));
        $formBuilder->add("position", "hidden", array("required" => false));
        $formBuilder->add("gateway", "hidden", array("required" => false));
        $formBuilder->add("title",  "text", array(
            "label" => "shipmentmethod.title",
            "required" => false
        ));
        $formBuilder->add("description", "textarea", array(
            "label" => "shipmentmethod.description",
            "required" => false
        ));
        $formBuilder->add("shipmentGroups", "entity", array(
            "label" => "shipmentmethod.shipmentGroups",
            'class' => 'tsCMSShopBundle:ShipmentGroup',
            'multiple' => true,
            'expanded' => true,
            "required" => false
        ));
        $formBuilder->add("enabled", "checkbox", array(
            "label" => "shipmentmethod.enabled",
            "required" => false
        ));
        $formBuilder->add('vatGroup', 'tscms_shop_vatgroup',array(
            'label' => 'product.vatGroup',
        ));
        $options = $formBuilder->create("options", null, array(
            "label" => "shipmentmethod.options",
            "compound" => true
        ));
        $gateway->getOptionForm($options);
        $formBuilder->add($options);

        $wrap->add($formBuilder);
        return $wrap;
    }

    /**
     * @Route("/autocomplete")
     * @Secure("ROLE_ADMIN")
     */
    public function autocompleteAction(Request $request) {
        $query = "%".$request->query->get("query", "")."%";
        /** @var ShipmentMethod[] $result */
        $result = $this->getDoctrine()->getEntityManager()->getRepository("tsCMSShopBundle:ShipmentMethod")->createQueryBuilder("p")->where("p.title LIKE :query")->getQuery()->setParameter("query", $query)->getResult();

        $data = array();
        foreach ($result as $row) {
            $data[] = array(
                "id" => $row->getId(),
                "title" => $row->getTitle(),
                "price" => 0
            );
        }
        return new JsonResponse($data);
    }
} 