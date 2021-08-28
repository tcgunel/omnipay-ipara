<?php

namespace Omnipay\Ipara\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Ipara\Message\CardInquiryRequest;
use Omnipay\Ipara\Message\CardInquiryResponse;
use Omnipay\Ipara\Message\CreateCardRequest;
use Omnipay\Ipara\Message\CreateCardResponse;
use Omnipay\Ipara\Models\CardInquiryRequestModel;
use Omnipay\Ipara\Models\CardInquiryResponseModel;
use Omnipay\Ipara\Models\CardModel;
use Omnipay\Ipara\Models\CreateCardRequestModel;
use Omnipay\Ipara\Models\CreateCardResponseModel;
use Omnipay\Ipara\Models\RequestHeadersModel;
use Omnipay\Ipara\Tests\TestCase;

class CreateCardTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function test_create_card_request()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/CreateCardRequest.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$data = $request->getData();

		$expected = [
			'request_params' => new CreateCardRequestModel([
				'userId'          => '612416047be0c612416047be0e',
				'cardOwnerName'   => 'Example User',
				'cardNumber'      => '5456165456165454',
				'cardAlias'       => 'Test Card Alias',
				'cardExpireMonth' => '12',
				'cardExpireYear'  => '24',
				'clientIp'        => '127.0.0.1',
			]),
			'headers'        => new RequestHeadersModel([
				'transactionDate' => '2021-08-27 16:17:02',
				'version'         => '1.0',
				'token'           => 'ZZZZZ3333333333:vzL/gp6of2ZaeZdOOubalSjbkTY=',
			]),
		];

		self::assertEquals($expected, $data);
	}

	public function test_create_card_request_validation_error()
	{
		$options = file_get_contents(__DIR__ . "/../Mock/CreateCardRequest-ValidationError.json");

		$options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

		$request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());

		$request->initialize($options);

		$this->expectException(InvalidRequestException::class);

		$request->getData();
	}

	public function test_create_card_response()
	{
		$httpResponse = $this->getMockHttpResponse('CreateCardResponseSuccess.txt');

		$response = new CreateCardResponse($this->getMockRequest(), $httpResponse);

		$data = $response->getData();

		$this->assertTrue($response->isSuccessful());

		$expected = new CreateCardResponseModel([
			'result'                => 1,
			'errorMessage'          => NULL,
			'responseMessage'       => 'Kartınız tanımlanmıştır.',
			'errorCode'             => NULL,
			'cardId'                => '002zqRWEV7ZHElKLj9cKS+z2rvr7OH80ByWUeSWMnqMFzE=',
			'maskNumber'            => '545616******5454',
			'alias'                 => 'Test Card Alias',
			'bankId'                => 111,
			'bankName'              => 'Finansbank',
			'cardFamilyName'        => 'CARD FINANS',
			'supportsInstallment'   => 0,
			'supportsInstallments'  => NULL,
			'supportedInstallments' => [1],
			'type'                  => 1,
			'serviceProvider'       => 1,
			'threeDSecureMandatory' => 1,
			'cvcMandatory'          => 1,
		]);

		$this->assertEquals($expected, $data);
	}

	public function test_create_card_response_api_error()
	{
		$httpResponse = $this->getMockHttpResponse('CreateCardResponseApiError.txt');

		$response = new CreateCardResponse($this->getMockRequest(), $httpResponse);

		$data = $response->getData();

		$this->assertFalse($response->isSuccessful());

		$expected = new CreateCardResponseModel([
			"result"          => 0,
			"errorMessage"    => "Kaydedilmek istenen banka/kredi kartı mağazada başka bir üyeliğe tanımlıdır. Lütfen mağaza ile iletişime geçiniz.",
			"responseMessage" => "Kaydedilmek istenen banka/kredi kartı mağazada başka bir üyeliğe tanımlıdır. Lütfen mağaza ile iletişime geçiniz.",
			"errorCode"       => 973,
		]);

		$this->assertEquals($expected, $data);
	}
}
