<?php

namespace App\Services;

require __DIR__ . '/../../afbonus/vendor/autoload.php';

use AfService\AfService;
use App\Models\AeroflotBonusLog;
use App\Models\Bill;
use App\Models\Product;
use Carbon\Carbon;
use Request;

class AeroflotBonusService {
	
	const CURRENCY_CODE = 643;
	const CONFIG_PATH = __DIR__ . '/../../afbonus/config.ini';
	const PARTNER_ID = 5164734;
	const LOCATION = '1IM';
	const TERMINAL = 1;
	const PARTNER_NAME = 'DREAM AERO';
	
	const REGISTERED_STATE = 0;
	const PAYED_STATE = 2; // для Списания - оплачено, для Начисления - заявка принята
	const CANCEL_STATE = 4;
	
	const TRANSACTION_TYPE_LIMITS = 'getInfo2';
	const TRANSACTION_TYPE_REGISTER_ORDER = 'registerOrder';
	const TRANSACTION_TYPE_ORDER_INFO = 'getOrderInfo';
	const TRANSACTION_TYPE_AUTH_POINTS = 'authpoints';
	
	const WRITEOFF_MILES_RATE = 4;
	const ACCRUAL_MILES_RATE = 50;
	
	const BOOKING_ACCRUAL_AFTER_DAYS = 14;
	const CERTIFICATE_PURCHASE_ACCRUAL_AFTER_DAYS = 60;
	
	/**
	 * @param Bill $bill
	 * @return mixed|null
	 */
	public static function registerOrder(Bill $bill)
	{
		try {
			$AfService = new AfService(parse_ini_file(self::CONFIG_PATH));
			
			$dateTime = date('YmdHis');
			$orderId = $dateTime . rand(10000, 99999);
			
			$position = $bill->positions()->first();
			$product = $position ? $position->product : null;
			
			$request = [
				'orderId' => $orderId,
				'amount' => $bill->aeroflot_bonus_amount * 100,
				'currency' => self::CURRENCY_CODE,
				'returnUrl' => Request::getSchemeAndHttpHost() . '/payment/' . ($bill->uuid ?? ''),
				'transactionDate' => $dateTime,
				'cheque' => [
					[
						'product' => $product ? $product->alias : $bill->number,
						'quantity' => 1,
						'amount' => $bill->amount * 100,
						'attributes' => [
							[
								'name' => 'AB_DISCOUNT',
							 	'value' => $bill->aeroflot_bonus_amount * 100
							]
						]
					]
				]
			];
			
			$result = $AfService->registerOrder($request);
			$result = json_decode(json_encode($result), true);
			
			$bill->aeroflot_transaction_order_id = $orderId;
			$bill->aeroflot_transaction_created_at = Carbon::parse($dateTime)->format('Y-m-d H:i:s');
			$bill->aeroflot_status = isset($result['status']['code']) ? $result['status']['code'] : null;
			$bill->save();
			
			$fields = [
				'bill_id' => $bill->id,
				'transaction_order_id' => $orderId,
				'transaction_created_at' => Carbon::parse($dateTime)->format('Y-m-d H:i:s'),
				'transaction_type' => 'registerOrder',
				'amount' => $bill->amount,
				'bonus_amount' => $bill->aeroflot_bonus_amount,
				'card_number' => $bill->aeroflot_card_number,
				'status' => isset($result['status']['code']) ? $result['status']['code'] : null,
				'request' => json_encode($request),
				'response' => json_encode($result),
			];
			self::addLog($fields);
			
			return $result;
		} catch (\Throwable $e) {
			\Log::channel('aeroflot')->info(__METHOD__ . ': ' . $e->getMessage() . '. Request: ' . $fields['request'] . '. Rsponse: ' . $fields['response']);
			
			return null;
		}
	}
	
	/**
	 * @param Bill $bill
	 * @return mixed|null
	 */
	public static function getOrderInfo(Bill $bill)
	{
		if ($bill->aeroflot_state == self::PAYED_STATE) return null;
		
		try {
			$AfService = new AfService(parse_ini_file(self::CONFIG_PATH));
			
			$dateTime = date('YmdHis');
			
			$request = [
				'orderId' => $bill->aeroflot_transaction_order_id,
				'transactionDate' => $dateTime,
			];
			$result = $AfService->getOrderInfo($request);
			$result = json_decode(json_encode($result), true);
			
			$status = isset($result['status']['code']) ? $result['status']['code'] : null;
			$state = isset($result['orderState']) ? $result['orderState'] : null;
			
			$bill->aeroflot_transaction_created_at = Carbon::parse($dateTime)->format('Y-m-d H:i:s');
			if ($bill->aeroflot_status != $status || $bill->aeroflot_state != $state) {
				$bill->aeroflot_status = $status;
				$bill->aeroflot_state = $state;
				if ($state == self::PAYED_STATE) {
					$bill->amount -= $bill->aeroflot_bonus_amount;
				}
			}
			$bill->save();
			
			$fields = [
				'bill_id' => $bill->id,
				'transaction_order_id' => $bill->aeroflot_transaction_order_id,
				'transaction_created_at' => Carbon::parse($dateTime)->format('Y-m-d H:i:s'),
				'transaction_type' => self::TRANSACTION_TYPE_ORDER_INFO,
				'amount' => $bill->amount,
				'bonus_amount' => $bill->aeroflot_bonus_amount,
				'card_number' => isset($result['cardNumber']) ? $result['cardNumber'] : null,
				'status' => isset($result['status']['code']) ? $result['status']['code'] : null,
				'state' => isset($result['orderState']) ? $result['orderState'] : null,
				'request' => json_encode($request),
				'response' => json_encode($result),
			];
			self::addLog($fields);

			return $result;
		} catch (\Throwable $e) {
			\Log::channel('aeroflot')->info(__METHOD__ . ': ' . $e->getMessage() . '. Request: ' . $fields['request'] . '. Rsponse: ' . $fields['response']);
			
			return null;
		}
	}
	
	/**
	 * @param $cardNumber
	 * @param Product $product
	 * @param $amount
	 * @return \Illuminate\Http\JsonResponse|null
	 */
	public static function getCardInfo($cardNumber, Product $product, $amount)
	{
		try {
			$AfService = new AfService(parse_ini_file(self::CONFIG_PATH));
			
			$dateTime = date('YmdHis');
			$transactionId = $dateTime . rand(10000, 99999);
			
			$request = [
				'transaction' => [
					'id' => $transactionId,
					'pan' => $cardNumber,
					'dateTime' => $dateTime,
				],
				'cheque' => [
					'item' => [
						'product' => $product->alias ?? '',
						'quantity' => 1,
						'amount' => $amount * 100,
					]
				],
				'currency' => self::CURRENCY_CODE,
			];
			
			$result = $AfService->getInfo2($request);
			$result = json_decode(json_encode($result), true);
			
			$result = [
				'max_limit' => isset($result['pointsAllocation']['maxChequePoints']) ? floor($result['pointsAllocation']['maxChequePoints'] / 100) : 0,
			];
			
			return $result;
		} catch (\Throwable $e) {
			\Log::channel('aeroflot')->info(__METHOD__ . ': ' . $e->getMessage() . '. Request: ' . $fields['request'] . '. Rsponse: ' . $fields['response']);
			
			return null;
		}
	}
	
	/**
	 * @param Bill $bill
	 * @return mixed|null
	 */
	public static function authPoints(Bill $bill)
	{
		try {
			$AfService = new AfService(parse_ini_file(self::CONFIG_PATH));
			
			$dateTime = date('YmdHis');
			$transactionId = $dateTime . rand(10000, 99999);
			
			$position = $bill->positions()->first();
			$product = $position ? $position->product : null;
			if (!$product) return null;
			
			$request = [
				'transaction' => [
					'id' => $transactionId,
					'pan' => $bill->aeroflot_card_number,
					'dateTime' => $dateTime,
					'extensions' => [
						[
							'type' => 'CHEQUE_MSG_REQ',
							'critical' =>'Y',
							'params' =>[
								[
									'name' => 'MSG_REQ',
									'value' => 'Y'
								]
							]
						],
						[
							'type' => 'PURCHASE_EXT_PARAMS',
							'critical' => 'Y',
							'params' => [
								[
									'name' => 'AFL_OP_TYPE',
									'value' => '1'
								],
								[
									'name' => 'TYPE_CARD',
									'value' => 'BASIC'
								],
								[
									'name' => 'ORDER_ID',
									'value' => $bill->uuid
								],
								[
									'name' => 'RECEIPT',
									'value' => $transactionId
								]
							]
						]
					]
				],
				'currency' => self::CURRENCY_CODE,
				'amount' => $bill->amount * 100,
				'payment' => [
					[
						'payMeans' => 'I',
						'amount' => $bill->amount * 100,
					]
				],
				'cheque' => [
					'item' => [
						'product' => $product->alias ?? '',
						'quantity' => 1,
						'amount' => $bill->amount * 100,
					]
				]
			];
			$result = $AfService->authpoints($request);
			$result = json_decode(json_encode($result), true);
			
			$bill->aeroflot_transaction_order_id = $transactionId;
			$bill->aeroflot_transaction_created_at = Carbon::parse($dateTime)->format('Y-m-d H:i:s');
			$bill->aeroflot_status = isset($result['status']['code']) ? $result['status']['code'] : null;
			$bill->aeroflot_state = isset($result['orderState']) ? $result['orderState'] : null;
			$bill->save();

			$fields = [
				'bill_id' => $bill->id,
				'transaction_order_id' => $transactionId,
				'transaction_created_at' => Carbon::parse($dateTime)->format('Y-m-d H:i:s'),
				'transaction_type' => self::TRANSACTION_TYPE_AUTH_POINTS,
				'amount' => $bill->amount,
				'bonus_amount' => $bill->aeroflot_bonus_amount,
				'card_number' => isset($result['cardNumber']) ? $result['cardNumber'] : null,
				'status' => isset($result['status']['code']) ? $result['status']['code'] : null,
				'state' => isset($result['orderState']) ? $result['orderState'] : null,
				'request' => json_encode($request),
				'response' => json_encode($result),
			];
			self::addLog($fields);
			
			return $result;
		} catch (\Throwable $e) {
			\Log::channel('aeroflot')->info(__METHOD__ . ': ' . $e->getMessage());
			
			return null;
		}
	}
	
	/**
	 * @param array $fields
	 * @return AeroflotBonusLog|null
	 */
	public static function addLog($fields = [])
	{
		try {
			$log = new AeroflotBonusLog();
			$log->bill_id = isset($fields['bill_id']) ? $fields['bill_id'] : 0;
			$log->transaction_order_id = isset($fields['transaction_order_id']) ? $fields['transaction_order_id'] : null;
			$log->transaction_created_at = isset($fields['transaction_created_at']) ? $fields['transaction_created_at'] : null;
			$log->transaction_type = isset($fields['transaction_type']) ? $fields['transaction_type'] : null;
			$log->amount = isset($fields['amount']) ? $fields['amount'] : 0;
			$log->bonus_amount = isset($fields['bonus_amount']) ? $fields['bonus_amount'] : 0;
			$log->card_number = isset($fields['card_number']) ? $fields['card_number'] : null;
			$log->status = isset($fields['status']) ? $fields['status'] : null;
			$log->state = isset($fields['state']) ? $fields['state'] : null;
			$log->request = isset($fields['request']) ? $fields['request'] : null;
			$log->response = isset($fields['response']) ? $fields['response'] : null;
			$log->save();
			
			return $log;
		} catch (\Throwable $e) {
			\Log::channel('aeroflot')->info(__METHOD__ . ': ' . $e->getMessage() . '. Request: ' . $fields['request'] . '. Rsponse: ' . $fields['response']);
			
			return null;
		}
	}
}