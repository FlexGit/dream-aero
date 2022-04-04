<?php

namespace App\Services;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\Models\Bill;

class PayAnyWayService {
	
	const BASE_URL = 'https://demo.moneta.ru'; // PROD https://www.payanyway.ru
	const PAY_REQUEST_URL = '/assistant.htm';
	const TEST_MODE = 1; // PROD 0
	const CURRENCY_CODE = 'RUB';
	const DATA_INTEGRITY_CHECK_CODE = 'Jr#47%Hdk';
	
	//private static $log = null;
	
	/**
	 * @param $payAccountNumber
	 * @param Bill $bill
	 * @return string
	 */
	public static function generatePayForm($payAccountNumber, Bill $bill) {
		$VIEW = view('pay-form', [
			'url' => self::BASE_URL . self::PAY_REQUEST_URL,
			'MNT_ID' => $payAccountNumber,
			'MNT_AMOUNT' => number_format($bill->amount, 2, '.', ''),
			'MNT_TRANSACTION_ID' => $bill->number,
			'MNT_CURRENCY_CODE' => self::CURRENCY_CODE,
			'MNT_TEST_MODE' => self::TEST_MODE,
			'MNT_DESCRIPTION' => 'Оплата по счету ' . $bill->number . ' на сумму ' . $bill->amount . ' ' . ($bill->currency ? $bill->currency->alias : 'RUB'),
			'MNT_SUBSCRIBER_ID' => $bill->contractor->uuid,
			'MNT_SIGNATURE' => md5($payAccountNumber . $bill->number . $bill->amount . self::CURRENCY_CODE . $bill->contractor->uuid . self::TEST_MODE . self::DATA_INTEGRITY_CHECK_CODE),
			'MNT_SUCCESS_URL' => route('home'), //paymentSuccess
			'MNT_FAIL_URL' => route('home'), //paymentFail
			'MNT_RETURN_URL' => route('home'),
		]);
		
		return (string)$VIEW;
	}
	
	public static function checkPayCallback(Request $request) {
		\Log::debug($request);
		\Log::debug($request->MNT_SIGNATURE . ' - ' . md5($request->MNT_ID . $request->MNT_TRANSACTION_ID . $request->MNT_OPERATION_ID . $request->MNT_AMOUNT . $request->MNT_CURRENCY_CODE . $request->MNT_SUBSCRIBER_ID . $request->MNT_TEST_MODE . self::DATA_INTEGRITY_CHECK_CODE));
		
		return $request->MNT_SIGNATURE == md5($request->MNT_ID . $request->MNT_TRANSACTION_ID . $request->MNT_OPERATION_ID . $request->MNT_AMOUNT . $request->MNT_CURRENCY_CODE . $request->MNT_SUBSCRIBER_ID . $request->MNT_TEST_MODE . self::DATA_INTEGRITY_CHECK_CODE);
	}
}