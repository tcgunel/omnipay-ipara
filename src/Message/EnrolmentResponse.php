<?php

namespace Omnipay\Ipara\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Ipara Enrolment Response
 *
 * @property EnrolmentRequest $request
 */
class EnrolmentResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful(): bool
    {
        return false;
    }

    public function isRedirect(): bool
    {
        return true;
    }

    public function getRedirectUrl()
    {
    	/** @var EnrolmentRequest $request */
    	$request = $this->getRequest();

        return $request->getEndpoint();
    }

    public function getRedirectMethod(): string
    {
        return 'POST';
    }

    public function getRedirectData(): array
    {
        $data = json_decode(json_encode($this->getData()), true);

        return [
            'parameters' => htmlspecialchars(json_encode($data)),
        ];
    }

}
