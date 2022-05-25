<?php

namespace App\Services;

require __DIR__ . '/../../afbonus/vendor/autoload.php';

use AfService\AfService;
use App\Models\AeroflotBonusLog;
use App\Models\DealPosition;
use App\Models\Product;
use Request;

class AeroflotBonusService {
	
	const CURRENCY_CODE = 643;
	const CONFIG_PATH = __DIR__ . '/../../afbonus/config.ini';
	const PARTNER_ID = 5164734;
	const LOCATION = '1IM';
	const TERMINAL = 1;
	
	const REGISTERED_STATE = 0;
	const PAYED_STATE = 2;
	const CANCEL_STATE = 4;
	
	const TRANSACTION_TYPE_LIMITS = 'getInfo2';
	const TRANSACTION_TYPE_REGISTER_ORDER = 'registerOrder';
	const TRANSACTION_TYPE_ORDER_INFO = 'getOrderInfo';
	const TRANSACTION_TYPE_AUTH_POINTS = 'authpoints';
	
	const MILES_RATE = 4;
	
	/**
	 * @param DealPosition $position
	 * @return mixed|null
	 */
	public static function registerOrder(DealPosition $position)
	{
		try {
			$AfService = new AfService(parse_ini_file(self::CONFIG_PATH));
			
			$dateTime = date('Ymdhis');
			$orderId = $dateTime . rand(10000, 99999);
			
			$request = [
				'orderId' => $orderId,
				'amount' => $position->aeroflot_bonus_amount * 100,
				'currency' => self::CURRENCY_CODE,
				'returnUrl' => Request::getSchemeAndHttpHost() . '/payment/' . ($position->bill->uuid ?? ''),
				'transactionDate' => $dateTime,
				'cheque' => [
					[
						'product' => $position->product->alias ?? '',
						'quantity' => 1,
						'amount' => $position->amount * 100,
						'attributes' => [
							[
								'name' => 'AB_DISCOUNT',
							 	'value' => $position->aeroflot_bonus_amount * 100
							]
						]
					]
				]
			];
			
			$result = $AfService->registerOrder($request);
			$result = json_decode(json_encode($result), true);
			
			$position->aeroflot_transaction_order_id = $orderId;
			$position->aeroflot_status = isset($result['status']['code']) ? $result['status']['code'] : null;
			$position->save();
			
			$fields = [
				'deal_position_id' => $position->id,
				'transaction_order_id' => $orderId,
				'transaction_type' => 'registerOrder',
				'amount' => $position->amount,
				'bonus_amount' => $position->aeroflot_bonus_amount,
				'card_number' => $position->aeroflot_card_number,
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
	 * @param DealPosition $position
	 * @return mixed|null
	 */
	public static function getOrderInfo(DealPosition $position)
	{
		try {
			$AfService = new AfService(parse_ini_file(self::CONFIG_PATH));
			
			$dateTime = date('Ymdhis');
			
			$request = [
				'orderId' => $position->aeroflot_transaction_order_id,
				'transactionDate' => $dateTime,
			];
			$result = $AfService->getOrderInfo($request);
			$result = json_decode(json_encode($result), true);
			
			$status = isset($result['status']['code']) ? $result['status']['code'] : null;
			$state = isset($result['orderState']) ? $result['orderState'] : null;
			
			if ($position->aeroflot_status != $status || $position->aeroflot_state != $state) {
				$position->aeroflot_status = $status;
				$position->aeroflot_state = $state;
				$position->save();
			}
			
			if ($position->aeroflot_state == self::PAYED_STATE) {
				$bill = $position->bill;
				if ($bill) {
					$amountDiff = $position->amount - $position->aeroflot_bonus_amount;
					$bill->amount = ($amountDiff >= 0) ? $amountDiff : 0;
					$bill->save();
					
					/*if ($bill->save()) {
						//dispatch(new \App\Jobs\SendPayLinkEmail($bill));
						$job = new \App\Jobs\SendPayLinkEmail($bill);
						$job->handle();
					}*/
				}
			}
			
			$fields = [
				'deal_position_id' => $position->id,
				'transaction_order_id' => $position->aeroflot_transaction_order_id,
				'transaction_type' => self::TRANSACTION_TYPE_ORDER_INFO,
				'amount' => $position->amount,
				'bonus_amount' => $position->aeroflot_bonus_amount,
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
			
			$dateTime = date('Ymdhis');
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
	 * @param DealPosition $position
	 * @return mixed|null
	 */
	public static function authPoints(DealPosition $position)
	{
		try {
			$AfService = new AfService(parse_ini_file(self::CONFIG_PATH));
			
			$dateTime = date('Ymdhis');
			$transactionId = $dateTime . rand(10000, 99999);
			
			$request = [
				'transaction' => [
					'id' => $transactionId,
					'pan' => $position->aeroflot_card_number,
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
									'value' => $position->uuid
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
				'amount' => $position->amount * 100,
				'payment' => [
					[
						'payMeans' => 'I',
						'amount' => $position->amount * 100,
					]
				],
				'cheque' => [
					'item' => [
						'product' => $position->product->alias,
						'quantity' => 1,
						'amount' => $position->amount * 100,
					]
				]
			];
			$result = $AfService->authpoints($request);
			$result = json_decode(json_encode($result), true);
			
			$fields = [
				'deal_position_id' => $position->id,
				'transaction_order_id' => $transactionId,
				'transaction_type' => self::TRANSACTION_TYPE_AUTH_POINTS,
				'amount' => $position->amount,
				'bonus_amount' => $position->aeroflot_bonus_amount,
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
	 * @param array $fields
	 * @return AeroflotBonusLog|null
	 */
	public static function addLog($fields = [])
	{
		try {
			$log = new AeroflotBonusLog();
			$log->deal_position_id = isset($fields['deal_position_id']) ? $fields['deal_position_id'] : 0;
			$log->transaction_order_id = isset($fields['transaction_order_id']) ? $fields['transaction_order_id'] : null;
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