<?php

namespace Omnipay\Ipara;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Ipara\Traits\PurchaseGettersSetters;

/**
 * Ipara Gateway
 * (c) Tolga Can Günel
 * 2015, mobius.studio
 * http://www.github.com/tcgunel/omnipay-ipara
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = [])
 */
class Gateway extends AbstractGateway
{
	use PurchaseGettersSetters;

	public function getName(): string
	{
		return 'Ipara';
	}

	public function getDefaultParameters()
	{
		return [
			"clientIp" => "127.0.0.1",

			"installment"   => "1",
			"nationalId"    => "11111111111",
			"taxNumber"     => "",
			"taxOffice"     => "",
			"userReference" => "",
			"secure"        => false,
			"publicKey"     => "",
			"privateKey"    => "",
			"language"      => ["tr-TR", "en-US"],
			"echo"          => "",
			"version"       => '1.0',

			"pageSize"  => '10',
			"pageIndex" => '1',

		];
	}

	public function purchase(array $parameters = [])
	{
		if (
			(array_key_exists('secure', $parameters) && $parameters["secure"] === true) ||
			$this->getSecure() === true
		) {

			return $this->createRequest('\Omnipay\Ipara\Message\EnrolmentRequest', $parameters);

		}

		return $this->createRequest('\Omnipay\Ipara\Message\ChargeRequest', $parameters);
	}

	public function authorize(array $parameters = [])
	{
		return $this->createRequest('\Omnipay\Ipara\Message\AuthorizeRequest', $parameters);
	}

	public function capture(array $parameters = [])
	{
		return $this->createRequest('\Omnipay\Ipara\Message\CaptureRequest', $parameters);
	}

	/**
	 * @param array $parameters
	 *
	 * @return AbstractRequest|\Omnipay\Common\Message\RequestInterface
	 *
	 * @deprecated No Need to confirm 3d payments with Ipara.
	 */
	public function completePurchase(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Ipara\Message\VerifyEnrolmentRequest', $parameters);
	}

	public function createCard(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Ipara\Message\CreateCardRequest', $parameters);
	}

	public function deleteCard(array $parameters = array())
	{
		return $this->createRequest('\Omnipay\Ipara\Message\DeleteCardRequest', $parameters);
	}

	public function listCard(array $parameters = array()): AbstractRequest
	{
		return $this->createRequest('\Omnipay\Ipara\Message\CardInquiryRequest', $parameters);
	}

	public function binLookup(array $parameters = array()): AbstractRequest
	{
		return $this->createRequest('\Omnipay\Ipara\Message\BinLookupRequest', $parameters);
	}

	public function paymentInquiry(array $parameters = array()): AbstractRequest
	{
		return $this->createRequest('\Omnipay\Ipara\Message\PaymentInquiryRequest', $parameters);
	}

	public function paymentSearch(array $parameters = array()): AbstractRequest
	{
		return $this->createRequest('\Omnipay\Ipara\Message\PaymentSearchRequest', $parameters);
	}

	public function login(array $parameters = array()): AbstractRequest
	{
		return $this->createRequest('\Omnipay\Ipara\Message\LoginRequest', $parameters);
	}

	public function createLink(array $parameters = array()): AbstractRequest
	{
		return $this->createRequest('\Omnipay\Ipara\Message\CreateLinkRequest', $parameters);
	}

	public function listLink(array $parameters = array()): AbstractRequest
	{
		return $this->createRequest('\Omnipay\Ipara\Message\ListLinkRequest', $parameters);
	}

	public function deleteLink(array $parameters = array()): AbstractRequest
	{
		return $this->createRequest('\Omnipay\Ipara\Message\DeleteLinkRequest', $parameters);
	}
}
