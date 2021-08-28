<?php

namespace Omnipay\Ipara\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Ipara\Helpers\Helper;
use Omnipay\Ipara\Models\ChargeRequestModel;
use Omnipay\Ipara\Models\EnrolmentRequestModel;
use Omnipay\Ipara\Models\RequestHeadersModel;
use Omnipay\Ipara\Models\ResponseModel;
use Omnipay\Ipara\Traits\PurchaseGettersSetters;

/**
 * Ipara Complete Auth Request
 *
 * @deprecated No need to confirm 3d payments with Ipara.
 */
class VerifyEnrolmentRequest extends ChargeRequest
{
    use PurchaseGettersSetters;

    /**
     * Get the XML registration string to be sent to the gateway
     *
     * @return array
     */
    public function getData(): array
    {
        /** @var array{request_params: ChargeRequestModel, headers: RequestHeadersModel} $data */
        $data = parent::getData();

        // Post back data.
        $returnedData = new ResponseModel($this->httpRequest->request->all());

        $data["request_params"]->threeDSecureCode = $returnedData->threeDSecureCode;

        $data["request_params"]->threeD           = true;
        $data["request_params"]->threeDSecureCode = $returnedData->threeDSecureCode;
        $data["request_params"]->orderId          = $returnedData->orderId;

        $data["headers"]->token = $this->token($data["request_params"]);

        return $data;
    }

    /**
     * @param EnrolmentRequestModel|ChargeRequestModel $request_model
     *
     * @return string
     */
    protected function token($request_model): string
    {
        $request_model = json_decode(json_encode($request_model), true);

        if ( ! isset($request_model["threeDSecureCode"])) {
            return "";
        }

        $hash_string =
            $this->getPrivateKey() .
            $request_model["orderId"] .
            $request_model["amount"] .
            $request_model["mode"] .
            $request_model["threeDSecureCode"] .
            ($this->getTransactionDate() ?? $this->transactionDateTime);

        return Helper::hash($this->getPublicKey(), $hash_string);
    }
}
