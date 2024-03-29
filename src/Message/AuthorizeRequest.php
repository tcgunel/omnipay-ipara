<?php

namespace Omnipay\Ipara\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Ipara\Models\ChargeRequestModel;
use Omnipay\Ipara\Models\PurchaseRequestModel;
use Omnipay\Ipara\Models\RequestHeadersModel;

class AuthorizeRequest extends PurchaseRequest
{
    protected $endpoint = "https://api.ipara.com/rest/payment/preauth";

    /**
     * @return array{request_params: ChargeRequestModel, headers: RequestHeadersModel}
     * @throws InvalidCreditCardException
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $data = parent::getData();

        $data["request_params"] = new PurchaseRequestModel($data["request_params"]);

        return $data;
    }

    /**
     * @throws \Omnipay\Ipara\Exceptions\OmnipayIparaHashValidationException
     */
    protected function createResponse($data): ChargeResponse
    {
        return $this->response = new ChargeResponse($this, $data);
    }

    /**
     * @param array{request_params: ChargeRequestModel, headers: RequestHeadersModel} $data
     *
     * @return ChargeResponse
     * @throws \Omnipay\Ipara\Exceptions\OmnipayIparaHashValidationException
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            array_merge((array)$data["headers"], [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]),
            json_encode($data["request_params"])
        );

        return $this->createResponse($httpResponse);
    }
}
