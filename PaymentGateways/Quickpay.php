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
            "protocol" => "7",
            "msgtype" => $authorize->getSubscription() ? "subscribe" : "authorize",
            "merchant" => $this->options['QuickpayID'],
            "language" => $this->options['language'],
            "ordernumber" => str_pad($order->getId(), 4, "0",STR_PAD_LEFT),
            "amount" => $authorize->getAmount(),
            "currency" => $authorize->getCurrency(),
            "continueurl" => $authorize->getGatewayUrls()->getSuccessUrl(),
            "cancelurl" => $authorize->getGatewayUrls()->getFailedUrl(),
            "callbackurl" => $authorize->getGatewayUrls()->getCallbackUrl(),
            "autocapture" => isset($this->options['autocapture']) && $this->options['autocapture'] == 1 ? 1 : 0
        );

        $md5String = "";
        foreach ($fields as $fieldValue) {
            $md5String .= $fieldValue;
        }
        $md5String .= $this->options["QuickpaySecret"];
        $fields["md5check"] = md5($md5String);

        $formBuilder->setAction("https://secure.quickpay.dk/form/");
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

        $data = array(
            "protocol" => 7,
            "msgtype" => "capture",
            "merchant" => $this->options['QuickpayID'],
            "amount" => $capture->getAmount(),
            "transaction" => $transactionId,
            "apikey" => $this->options['QuickpayAPI']
        );

        $data['md5check'] = md5(
            $data['protocol'] .
            $data['msgtype'] .
            $data['merchant'] .
            $data['amount'] .
            $data['transaction'] .
            $data['apikey'] .
            $this->options['QuickpaySecret']
        );

        $curl = new CURL("https://secure.quickpay.dk/api");
        $response = $curl->params($data)->post();

        $xml = simplexml_load_string($response);
        $state = (string)current($xml->xpath("state"));

        $result = new PaymentResult();
        $result->setTransactionId($transactionId);

        if ($state == 3) {
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

        $data = array(
            "protocol" => 7,
            "msgtype" => "status",
            "merchant" => $this->options['QuickpayID'],
            "transaction" => $transactionId,
            "apikey" => $this->options['QuickpayAPI']
        );

        $data['md5check'] = md5(
            $data['protocol'] .
            $data['msgtype'] .
            $data['merchant'] .
            $data['transaction'] .
            $data['apikey'] .
            $this->options['QuickpaySecret']
        );

        $curl = new CURL("https://secure.quickpay.dk/api");
        $response = $curl->params($data)->post();

        $xml = new SimpleXMLElement($response);
        $state = $xml->state;

        $status = null;
        switch ($state) {
            case 1:
                $status = PaymentStatus::AUTHORIZED;
                break;
            case 3:
                $status = PaymentStatus::CAPTURED;
                break;
            case 7:
                $status = PaymentStatus::REFUNDED;
                break;
            case 9:
                $status = PaymentStatus::SUBSCRIBED;
                break;
            default:
                $status = PaymentStatus::UNKNOWN;
        }

        $authorizedAmount = 0;
        $capturedAmount = 0;
        $refundedAmount = 0;


        foreach ($xml->history as $history) {
            $msgtype = $history->msgtype;
            $amount = $history->amount;
            switch ($msgtype) {
                case "authorize":
                    $authorizedAmount += intval($amount);
                    break;
                case "capture":
                    $capturedAmount += intval($amount);
                    break;
                case "refund":
                    $refundedAmount += intval($amount);
                    break;
            }
        }


        return new PaymentStatus($status,$authorizedAmount, $capturedAmount, $refundedAmount);
    }

    public function callback(Order $order, Request $request)
    {
        $params = $request->request->all();
        $md5String = "";
        if(isset($params["msgtype"])){$md5String .= $params["msgtype"]; }
        if(isset($params["ordernumber"])){$md5String .= $params["ordernumber"]; }
        if(isset($params["amount"])){$md5String .= $params["amount"]; }
        if(isset($params["currency"])){$md5String .= $params["currency"]; }
        if(isset($params["time"])){$md5String .= $params["time"]; }
        if(isset($params["state"])){$md5String .= $params["state"]; }
        if(isset($params["qpstat"])){$md5String .= $params["qpstat"]; }
        if(isset($params["qpstatmsg"])){$md5String .= $params["qpstatmsg"]; }
        if(isset($params["chstat"])){$md5String .= $params["chstat"]; }
        if(isset($params["chstatmsg"])){$md5String .= $params["chstatmsg"]; }
        if(isset($params["merchant"])){$md5String .= $params["merchant"]; }
        if(isset($params["merchantemail"])){$md5String .= $params["merchantemail"]; }
        if(isset($params["transaction"])){$md5String .= $params["transaction"]; }
        if(isset($params["cardtype"])){$md5String .= $params["cardtype"]; }
        if(isset($params["cardnumber"])){$md5String .= $params["cardnumber"]; }
        if(isset($params["cardhash"])){$md5String .= $params["cardhash"]; }
        if(isset($params["cardexpire"])){$md5String .= $params["cardexpire"]; }
        if(isset($params["acquirer"])){$md5String .= $params["acquirer"]; }
        if(isset($params["splitpayment"])){$md5String .= $params["splitpayment"]; }
        if(isset($params["fraudprobability"])){$md5String .= $params["fraudprobability"]; }
        if(isset($params["fraudremarks"])){$md5String .= $params["fraudremarks"]; }
        if(isset($params["fraudreport"])){$md5String .= $params["fraudreport"]; }
        if(isset($params["fee"])){$md5String .= $params["fee"]; }
        if(isset($params["secret"])){ $md5String .= $params["secret"]; }

        $md5 = md5($md5String);
        if ($md5 == $params['md5check']) {
            throw new \InvalidArgumentException("MD5 does not match");
        }

        $result = new PaymentResult();

        $state = $params['state'];
        switch ($state) {
            case 1:
            case 3:
                $result->setType(PaymentResult::AUTHORIZE);
                break;
            case 7:
                $result->setType(PaymentResult::REFUND);
                break;
            case 9:
                $result->setType(PaymentResult::SUBSCRIPTION);
                break;
            default:
                $result->setType(PaymentResult::ERROR);
        }

        if ($state == 3) {
            $result->setCaptured(true);
        }

        if ($params['amount'] != $order->getTotalVat()) {
            $result->setType(PaymentResult::ERROR);
        }

        $result->setTransactionId($params['transaction']);

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