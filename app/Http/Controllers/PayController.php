<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\ProductType;
use App\Models\Product;
use App\Models\City;
use App\Models\Deal;
use App\Models\Payment;

use App\Services\PayAnyWayService;

class PayController extends Controller {
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * @param $id
	 * @param $cityId
	 * @return \Illuminate\Http\JsonResponse|null
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function sendPayRequest($id, $cityId) {
		$payment = Payment::where('is_active', true)
			->find($id);
		if(!$payment) {
			return response()->json(['status' => 'error', 'reason' => 'Счет не существует']);
		}
		
		if ($payment->status_id != Payment::STATUSES['not_payed']) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректный статус счета для оплаты']);
		}
		
		$city = City::where('is_active', true)
			->find($cityId);
		if(!$payment) {
			return response()->json(['status' => 'error', 'reason' => 'Город не существует']);
		}
		
		$payAccountId = $city->pay_account_id ?? 0;
		if (!$payAccountId) {
			return response()->json(['status' => 'error', 'reason' => 'Некорректный номер счета платежной системы']);
		}
		
		$dealId = $payment->deal_id ?? 0;
		if (!$dealId) {
			return response()->json(['status' => 'error', 'reason' => 'Счет не привязан к сделке']);
		}
		
		$deal = Deal::where('is_active', true)
			->find($dealId);
		if(!$deal) {
			return response()->json(['status' => 'error', 'reason' => 'Сделка не существует']);
		}
		
		$result = PayAnyWayService::sendPayRequest($payAccountId, $payment, $deal);
		
		return $result;
	}
	
	/**
	 * @return string
	 */
	public function payCallback() {
		$result = PayAnyWayService::checkPayCallback($this->request);
		
		if (!$result) {
			return 'FAIL';
		}

		$paymentId = $this->request->MNT_TRANSACTION_ID ?? 0;
		if ($paymentId) {
			$payment = Payment::find($paymentId);
			if ($payment && $payment->amount == $this->request->MNT_AMOUNT) {
			
			}
		}
		
		return 'SUCCESS';
	}
	
	public function paySuccess() {
		return 'success';
	}
	
	public function payFail() {
		return 'fail';
	}
	
	public function payReturn() {
		return 'cancel';
	}
}