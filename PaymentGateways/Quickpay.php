<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/16/14
 * Time: 10:38 PM
 */

namespace tsCMS\ShopBundle\PaymentGateways;


use BCA\CURL\CURL;
use DOMDocument;
use SimpleXMLElement;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use tsCMS\ShopBundle\Entity\Order;
use tsCMS\ShopBundle\Entity\PaymentTransaction;
use tsCMS\ShopBundle\Interfaces\PaymentGatewayInterface;
use tsCMS\ShopBundle\Model\PaymentAuthorize;
use tsCMS\ShopBundle\Model\PaymentCapture;
use tsCMS\ShopBundle\Model\PaymentRefund;
use tsCMS\ShopBundle\Model\PaymentResult;
use tsCMS\ShopBundle\Model\PaymentStatus;
use tsCMS\ShopBundle\Model\Statuses;

class Quickpay implements PaymentGatewayInterface
{
    private $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getName()
    {
        return "paymentgateway.quickpay.name";
    }

    public function getDescription()
    {
        return "paymentgateway.quickpay.description";
    }

    public function getOptionForm(FormBuilderInterface $formBuilder)
    {
        $formBuilder->add("QuickpayID","text", array(
            "label" => "paymentgateway.quickpay.quickpayid",
            "required" => false
        ));
        $formBuilder->add("QuickpayAgreement","text", array(
            "label" => "paymentgateway.quickpay.quickpayagreementid",
            "required" => false
        ));
        $formBuilder->add("QuickpaySecret","text", array(
            "label" => "paymentgateway.quickpay.quickpaysecret",
            "required" => false
        ));
        $formBuilder->add("QuickpayAPI","text", array(
            "label" => "paymentgateway.quickpay.quickpayapi",
            "required" => false
        ));
        $formBuilder->add("autocapture","checkbox", array(
            "label" => "paymentgateway.quickpay.autocapture",
            "required" => false
        ));
        $formBuilder->add("language", "choice", array(
            "label" => "paymentgateway.quickpay.language",
            "required" => false,
            "choices" => array(
                "da" => "Danish",
                "de" => "German",
                "en" => "English",
                "es" => "Spanish",
                "fo" => "Faeroese",
                "fi" => "Finnish",
                "fr" => "French",
                "kl" => "Greenlandish",
                "it" => "Italian",
                "no" => "Norwegian",
                "nl" => "Dutch",
                "pl" => "Polish",
                "pt" => "Portuguese",
                "ru" => "Russian",
                "sv" => "Swedish"
            )
        ));
    }

    public function getAuthorizeForm(FormBuilderInterface $formBuilder, PaymentAuthorize $authorize, Order $order)
    {
        $fields = array(
            "version" => "v10",
            "merchant_id" => $this->options['QuickpayID'],
            "agreement_id" => $this->options['QuickpayAgreement'],
            "language" => $this->options['language'],
            "order_id" => str_pad($order->getId(), 4, "0",STR_PAD_LEFT),
            "amount" => $authorize->getAmount(),
            "currency" => $authorize->getCurrency(),
            "continueurl" => $authorize->getGatewayUrls()->getSuccessUrl(),
            "cancelurl" => $authorize->getGatewayUrls()->getFailedUrl(),
            "callbackurl" => $authorize->getGatewayUrls()->getCallbackUrl(),
            "autocapture" => isset($this->options['autocapture']) && $this->options['autocapture'] == 1 ? 1 : 0
        );


        ksort($fields);
        $values = array();
        foreach ($fields as $fieldValue) {
            $values[] = $fieldValue;
        }
        $secret = $this->options["QuickpaySecret"];
        $fields["checksum"] = hash_hmac("sha256", implode(" ", $values), $secret);

        $formBuilder->setAction("https://payment.quickpay.net");
        $formBuilder->setData($fields);
        foreach (array_keys($fields) as $name) {
            $formBuilder->add($name,"hidden");
        }
    }

    public function capture(Order $order, PaymentCapture $capture)
    {
        $transactionId = null;
        foreach ($order->getPaymentTransactions() as $transaction) {
            /** @var PaymentTransaction $transaction */
            if ($transaction->getPaymentMethod()->getGateway() == "Quickpay") {
                if ($transaction->getType() == PaymentResult::AUTHORIZE && !$transaction->getCaptured()) {
                    $transactionId = $transaction->getTransactionId();
                }
            }
        }

        if (!$transactionId) {
            $result = new PaymentResult();
            $result->setType(PaymentResult::ERROR);
            $result->setCaptured(false);
            $result->setMessage("Quickpay transaction id not found in history");
            return $result;
        }


        $curl = new CURL("https://api.quickpay.net/payments/".$transactionId."/capture");
        $curl->header("Accept-Version","v10");
        $curl->auth("",$this->options["QuickpayAPI"], "basic");
        $curl->header("Accept","application/json");
        $curl->header("Content-Type","application/json");
        $response = $curl->post(json_encode(array("amount" => $capture->getAmount())));

        $data = json_decode($response);
        $operation = $data->operations[0];

        $result = new PaymentResult();
        $result->setTransactionId($transactionId);

        if ($operation->type == "capture") {
            $result->setType(PaymentResult::AUTHORIZE);
            $result->setCaptured(true);
        } else {
            $result->setType(PaymentResult::ERROR);
            $result->setCaptured(false);
        }

        return $result;
    }

    public function refund(Order $order, PaymentRefund $refund)
    {
        return "";
    }

    public function status(Order $order)
    {
        $transactionId = null;
        foreach ($order->getPaymentTransactions() as $transaction) {
            /** @var PaymentTransaction $transaction */
            if ($transaction->getPaymentMethod()->getGateway() == "Quickpay") {
                if ($transaction->getType() == PaymentResult::AUTHORIZE && !$transaction->getCaptured()) {
                    $transactionId = $transaction->getTransactionId();
                }
            }
        }

        if (!$transactionId) {
            $result = new PaymentResult();
            $result->setType(PaymentResult::ERROR);
            $result->setCaptured(false);
            $result->setMessage("Quickpay transaction id not found in history");
            return $result;
        }


        $curl = new CURL("https://api.quickpay.net/payments/".$transactionId);
        $curl->header("Accept-Version","v10");
        $curl->auth("",$this->options["QuickpayAPI"],"basic");
        $curl->header("Accept","application/json");
        $response = $curl->get();
        $data = json_decode($response);

        $operation = $data->operations[count($data->operations) - 1];
        $type = $operation->type;
        switch ($type) {
            case "authorize":
                $status = PaymentStatus::AUTHORIZED;
                break;
            case "capture":
                $status = PaymentStatus::CAPTURED;
                break;
            default:
                $status = PaymentResult::ERROR;
        }

        $authorizedAmount = 0;
        $capturedAmount = 0;
        $refundedAmount = 0;
        foreach ($data->operations as $operation) {
            if ($operation->type== "authorize") {
                $authorizedAmount += $operation->amount;
            } else if ($operation->type == "capture") {
                $capturedAmount += $operation->amount;
            } elseif ($operation->type== "refund") {
                $refundedAmount += $operation->amount;
            }
        }

        $response = new PaymentStatus($status, $authorizedAmount, $capturedAmount, $refundedAmount);
        return $response;
    }

    public function callback(Order $order, Request $request)
    {
        $headers = $request->headers->all();
        $request_body = file_get_contents("php://input");

        $checksum = $headers["QuickPay-Checksum-Sha256"];
        $hash = hash_hmac("sha256", $request_body, $this->options["QuickpaySecret"]);

        if ($checksum == $hash) {
            throw new \InvalidArgumentException("Checksum does not match");
        }
        $result = new PaymentResult();

        $data = json_decode($request_body);
        $operation = $data->operations[count($data->operations) - 1];
        $type = $operation->type;
        switch ($type) {
            case "authorize":
                $result->setType(PaymentResult::AUTHORIZE);
                $result->setCaptured(false);
                break;
            case "capture":
                $result->setType(PaymentResult::AUTHORIZE);
                $result->setCaptured(true);
                break;
            default:
                $result->setType(PaymentResult::ERROR);
        }

        $accepted = $data->accepted;
        if (!$accepted) {
            $result->setType(PaymentResult::ERROR);
        }

        if ($operation->amount != $order->getTotalVat()) {
            $result->setType(PaymentResult::ERROR);
        }

        $result->setTransactionId($data->id);


        return $result;
    }

    /**
     * @return boolean
     */
    public function allowManualStatusChange()
    {
        return false;
    }

    /**
     * @param Order $order
     * @return int
     */
    public function possibleCaptureAmount(Order $order)
    {
        $status = $this->status($order);
        if ($status->getCurrentState() == PaymentStatus::AUTHORIZED) {
            return $status->getAuthorizedAmount() - $status->getCapturedAmount();
        }
        return 0;
    }

    public function calculatePrice(Order $order)
    {
        return 0;
    }
}