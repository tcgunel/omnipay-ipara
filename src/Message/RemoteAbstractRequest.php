<?php

namespace Omnipay\Ipara\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Ipara\Traits\PurchaseGettersSetters;

/**
 * Ipara Purchase Request
 */
abstract class RemoteAbstractRequest extends AbstractRequest
{
    use PurchaseGettersSetters;

    protected function get_card($key)
    {
        return $this->getCard() ? $this->getCard()->$key() : null;
    }

    abstract protected function createResponse($data);
}
