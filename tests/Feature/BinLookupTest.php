<?php

namespace Omnipay\Ipara\Tests\Feature;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Ipara\Message\BinLookupRequest;
use Omnipay\Ipara\Message\BinLookupResponse;
use Omnipay\Ipara\Message\ChargeRequest;
use Omnipay\Ipara\Message\ChargeResponse;
use Omnipay\Ipara\Models\AddressModel;
use Omnipay\Ipara\Models\BinLookupRequestModel;
use Omnipay\Ipara\Models\BinLookupResponseModel;
use Omnipay\Ipara\Models\ChargeRequestModel;
use Omnipay\Ipara\Models\InvoiceAddressModel;
use Omnipay\Ipara\Models\ProductModel;
use Omnipay\Ipara\Models\PurchaserModel;
use Omnipay\Ipara\Models\RequestHeadersModel;
use Omnipay\Ipara\Models\ResponseModel;
use Omnipay\Ipara\Tests\TestCase;

class BinLookupTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function test_bin_lookup_request()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/BinLookupRequest.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		$expected = [
			'request_params' => new BinLookupRequestModel([
				'amount'    => 100,
				'binNumber' => '545616',
				'threeD'    => true,
			]),
			'headers'        => new RequestHeadersModel([
				'transactionDate' => "2021-08-27 16:17:02",
				'version'         => '1.0',
				'token'           => 'ZZZZZ3333333333:zcDsZ6j2ie8RynpG0TmPDs9nGXo=',
			]),
		];

		self::assertEquals($expected, $data);
	}

	public function test_bin_lookup_request_validation_error()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/BinLookupRequest-ValidationError.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$this->expectException(InvalidCreditCardException::class);

		$request->getData();
	}

	public function test_bin_lookup_response()
	{
		$httpResponse = $this->getMockHttpResponse('BinLookupResponseSuccess.txt');

		$response = new BinLookupResponse($this->getMockRequest(), $httpResponse);

		$data = $response->getData();

		$this->assertTrue($response->isSuccessful());

		$this->assertEquals(new BinLookupResponseModel([
			'bankId'                        => 111,
			'bankName'                      => 'Finansbank',
			'cardFamilyName'                => 'CARD FINANS',
			'supportsInstallment'           => 0,
			'type'                          => 1,
			'serviceProvider'               => 1,
			'result'                        => 1,
			'cardThreeDSecureMandatory'     => 0,
			'merchantThreeDSecureMandatory' => 1,
			'cvcMandatory'                  => 1,
			'businessCard'                  => 0,
			'installmentDetail'             => [
				[
					'requiredAmount'           => '100',
					'requiredCommissionAmount' => '000',
					'installment'              => 1,
					'merchantCommissionRate'   => '0.05',
				],
			],
			'supportsAgriculture'           => 0,
		]), $data);
	}

	public function test_charge_response_api_error()
	{
		$httpResponse = $this->getMockHttpResponse('BinLookupResponseApiError.txt');

		$response = new BinLookupResponse($this->getMockRequest(), $httpResponse);

		$data = $response->getData();

		$this->assertFalse($response->isSuccessful());

		$this->assertEquals(new BinLookupResponseModel([
			'result'                        => 0,
			'errorMessage'                  => 'Bin lookup failed.',
			'errorCode'                     => NULL,
			'bankId'                        => NULL,
			'bankName'                      => NULL,
			'cardFamilyName'                => NULL,
			'supportsInstallment'           => NULL,
			'supportedInstallments'         => NULL,
			'type'                          => NULL,
			'serviceProvider'               => NULL,
			'cardThreeDSecureMandatory'     => NULL,
			'merchantThreeDSecureMandatory' => NULL,
			'cvcMandatory'                  => NULL,
			'businessCard'                  => NULL,
			'installmentDetail'             => NULL,
		]), $data);
	}
}
