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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\Category;
use tsCMS\ShopBundle\Entity\Paymentmethod;
use tsCMS\ShopBundle\Form\CategoryType;
use tsCMS\ShopBundle\Interfaces\PaymentGatewayInterface;
use tsCMS\ShopBundle\Services\PaymentService;
use tsCMS\ShopBundle\Services\ShopService;

/**
 * @Route("/shop/payment")
 */
class PaymentController extends Controller {
    /**
     * @Route("")
     * @Secure("ROLE_ADMIN")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        /** @var PaymentService $PaymentService */
        $PaymentService = $this->get("tsCMS_shop.paymentservice");

        /** @var PaymentMethod[] $PaymentMethods */
        $PaymentMethods = $PaymentService->getPaymentMethods();

        if ($request->isMethod("pOST")) {
            $methods = $request->request->get("form",array());

            $formBuilder = $this->createFormBuilder();
            $data = array();

            $found = array();

            foreach ($methods as $index => $method) {
                if (!is_array($method)) {
                    continue;
                }

                $PaymentMethod = null;
                if ($method['id']) {
                    $found[] = $method['id'];

                    foreach ($PaymentMethods as $aPaymentMethod) {
                        if ($aPaymentMethod->getId() == $method['id']) {
                            $PaymentMethod = $aPaymentMethod;
                            break;
                        }
                    }
                }
                if (!$PaymentMethod) {
                    $PaymentMethod = new PaymentMethod();
                    $this->getDoctrine()->getManager()->persist($PaymentMethod);
                }

                $data[$index] = $PaymentMethod;
                $gateway = $PaymentService->getPaymentGateway($method['gateway']);
                $this->getGatewayOptionForm($gateway, $index, $PaymentMethod, $formBuilder);

            }

            $form = $formBuilder->getForm();
            $form->setData($data);
            $form->handleRequest($request);

            if ($form->isValid()) {
                foreach ($PaymentMethods as $PaymentMethod) {
                    if (!in_array($PaymentMethod->getId(), $found)) {
                        $PaymentMethod->setDeleted(true);
                    }
                }

                $this->getDoctrine()->getManager()->flush();
            }

            return $this->redirect($this->generateUrl("tscms_shop_payment_index"));
        }


        $formBuilder = $this->createFormBuilder();

        $populatedPaymentmethods = array();
        foreach ($PaymentMethods as $index => $PaymentMethod) {
            $gateway = $PaymentService->getPaymentGateway($PaymentMethod);

            $populatedPaymentmethods[] = array(
                "method" => $PaymentMethod,
                "gateway" => $gateway
            );
            $this->getGatewayOptionForm($gateway, $index, $PaymentMethod, $formBuilder);
        }

        return array(
            "paymentForm" => $formBuilder->getForm()->createView(),
            "paymentMethods" => $populatedPaymentmethods,
            "availableGateways" => $this->getAvailableGateways()
        );
    }

    /**
     * @return PaymentGatewayInterface[]
     */
    private function getAvailableGateways() {
        /** @var PaymentService $PaymentService */
        $PaymentService = $this->get("tsCMS_shop.paymentservice");

        $finder = new Finder();
        $gateways = array();
        foreach ($finder->files()->in(__DIR__."/../PaymentGateways") as $file) { /** @var $file SplFileInfo */
            $gateway = $PaymentService->getPaymentGateway($file->getBasename(".".$file->getExtension()));

            $PaymentMethod = new PaymentMethod();
            $PaymentMethod->setGateway($file->getBasename(".".$file->getExtension()));
            $gateways[] = array(
                'gateway' => $gateway,
                'optionForm' => $this->getGatewayOptionForm($gateway,null,$PaymentMethod)->getForm()->createView()
            );
        }

        return $gateways;
    }

    /**
     * @param PaymentGatewayInterface $gateway
     * @return \Symfony\Component\Form\FormBuilder
     */
    private function getGatewayOptionForm(PaymentGatewayInterface $gateway, $index, PaymentMethod $Paymentmethod, $wrap = null) {
        if (!$wrap) {
            $wrap = $this->createFormBuilder();
        }

        $prefix = $index !== null ? $index : "_P_";

        $formBuilder = $wrap->create($prefix, null, array(
            "compound" => true,
            "data_class" => "tsCMS\\ShopBundle\\Entity\\PaymentMethod"
        ));
        $formBuilder->setData($Paymentmethod);
        $formBuilder->add("id", "hidden", array("required" => false));
        $formBuilder->add("position", "hidden", array("required" => false));
        $formBuilder->add("gateway", "hidden", array("required" => false));
        $formBuilder->add("title",  "text", array(
            "label" => "paymentmethod.title"
        ));
        $formBuilder->add("description", "textarea", array(
            "label" => "paymentmethod.description",
            "required" => false
        ));
        $formBuilder->add("enabled", "checkbox", array(
            "label" => "paymentmethod.enabled",
            "required" => false
        ));
        $formBuilder->add('vatGroup', 'tscms_shop_vatgroup',array(
            'label' => 'product.vatGroup',
        ));
        $options = $formBuilder->create("options", null, array(
            "label" => "paymentmethod.options",
            "compound" => true
        ));
        $gateway->getOptionForm($options);
        $formBuilder->add($options);

        $wrap->add($formBuilder);
        return $wrap;
    }
} 