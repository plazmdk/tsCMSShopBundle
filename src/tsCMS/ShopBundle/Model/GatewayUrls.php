<?php
/**
 * Created by PhpStorm.
 * User: plazm
 * Date: 7/23/14
 * Time: 8:24 AM
 */

namespace tsCMS\ShopBundle\Model;


class GatewayUrls {
    private $callbackUrl;
    private $successUrl;
    private $failedUrl;

    function __construct($callbackUrl, $failedUrl, $successUrl)
    {
        $this->callbackUrl = $callbackUrl;
        $this->failedUrl = $failedUrl;
        $this->successUrl = $successUrl;
    }

    /**
     * @return mixed
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @return mixed
     */
    public function getFailedUrl()
    {
        return $this->failedUrl;
    }

    /**
     * @return mixed
     */
    public function getSuccessUrl()
    {
        return $this->successUrl;
    }


} 