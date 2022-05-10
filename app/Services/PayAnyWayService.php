<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Bill;

class PayAnyWayService {
	
	const BASE_URL = 'https://www.payanyway.ru'; // PROD https://www.payanyway.ru DEV https://demo.moneta.ru
	const PAY_REQUEST_URL = '/assistant.htm';
	const TEST_MODE = 0; // PROD 0 DEV 1
	const CURRENCY_CODE = 'RUB';
	const DATA_INTEGRITY_CHECK_CODE = 'da20181011pay'; // PROD da20181011pay DEV Jr#47%Hdk
	
	/**
	 * @param Bill $bill
	 * @return string
	 */
	public static function generatePaymentForm(Bill $bill) {
		$params = [
			'url' => self::BASE_URL . self::PAY_REQUEST_URL,
			'MNT_ID' => $bill->location->pay_account_number,
			'MNT_AMOUNT' => number_format($bill->amount, 2, '.', ''),
			'MNT_TRANSACTION_ID' => $bill->uuid,
			'MNT_CURRENCY_CODE' => self::CURRENCY_CODE,
			'MNT_TEST_MODE' => self::TEST_MODE,
			'MNT_DESCRIPTION' => 'Оплата по счету ' . $bill->number,
			'MNT_SUBSCRIBER_ID' => $bill->contractor->uuid,
			'MNT_SUCCESS_URL' => request()->getSchemeAndHttpHost() . '/payment/success',
			'MNT_FAIL_URL' => request()->getSchemeAndHttpHost() . '/payment/fail',
			'MNT_RETURN_URL' => request()->getSchemeAndHttpHost(),
			'unitId' => 'card',
		];
		
		$params['MNT_SIGNATURE'] = md5($params['MNT_ID'] . $params['MNT_TRANSACTION_ID'] . $params['MNT_AMOUNT'] . $params['MNT_CURRENCY_CODE'] . $params['MNT_SUBSCRIBER_ID'] . $params['MNT_TEST_MODE'] . self::DATA_INTEGRITY_CHECK_CODE);
		
		$VIEW = view('payment-form', $params);
		
		return (string)$VIEW;
	}
	
	/**
	 * @param Request $request
	 * @return bool
	 */
	public static function paymentCallback(Request $request) {
		//\Log::debug($request);
		\Log::debug($request->MNT_SIGNATURE . ' - ' . md5($request->MNT_ID . $request->MNT_TRANSACTION_ID . $request->MNT_OPERATION_ID . $request->MNT_AMOUNT . $request->MNT_CURRENCY_CODE . $request->MNT_SUBSCRIBER_ID . $request->MNT_TEST_MODE . self::DATA_INTEGRITY_CHECK_CODE));
		
		return $request->MNT_SIGNATURE == md5($request->MNT_ID . $request->MNT_TRANSACTION_ID . $request->MNT_OPERATION_ID . $request->MNT_AMOUNT . $request->MNT_CURRENCY_CODE . $request->MNT_SUBSCRIBER_ID . $request->MNT_TEST_MODE . self::DATA_INTEGRITY_CHECK_CODE);
	}
}