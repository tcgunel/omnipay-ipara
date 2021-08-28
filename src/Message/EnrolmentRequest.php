<?php

namespace Omnipay\Ipara\Message;

use Omnipay\Ipara\Models\EnrolmentRequestModel;
use Omnipay\Ipara\Models\RequestHeadersModel;

/**
 * Ipara 3D Secure enrolment request
 */
class EnrolmentRequest extends PurchaseRequest
{
    protected $endpoint = "https://api.ipara.com/rest/payment/threed";

    /**
     * Get the XML registration string to be sent to the gateway
     *
     * @return array{request_params: array, headers: RequestHeadersModel}
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $data = parent::getData();

        $this->validate("secure", "returnUrl", "cancelUrl");

        $data["request_params"] = new EnrolmentRequestModel($data["request_params"]);

        $data["request_params"]->token = $this->token($data["request_params"]);

        return $data;
    }

    /**
     * @throws \Omnipay\Ipara\Exceptions\OmnipayIparaHashValidationException
     */
    public function sendData($data)
    {
        return $this->response = new EnrolmentResponse($this, $data["request_params"]);
    }

    protected function createResponse($data): EnrolmentResponse
    {

    }
}
