<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\City;
use App\Models\Contractor;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\LegalEntity;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Status;
use App\Models\Token;

class HelpFunctions {
	/**
	 * @param $entity
	 * @param $alias
	 * @return mixed
	 */
	public static function getModelAttributeName($entity, $alias)
	{
		if (!defined(get_class(app('App\Models\\' . $entity)) . '::ATTRIBUTES')) return $alias;
		if (!array_key_exists($alias, app('App\Models\\' . $entity)::ATTRIBUTES)) return $alias;
		
		return app('App\Models\\' . $entity)::ATTRIBUTES[$alias];
	}
	
	/**
	 * @param $entity
	 * @param $data
	 * @param $output
	 * @return string
	 */
	public static function outputDiffTypeData($entity, $data, $output)
	{
		if (!$data) return $output;
		
		if (!self::isAssoc($data)) return implode(', ', $data);
		
		foreach ($data ?? [] as $key => $value) {
			$output .= self::getModelAttributeName($entity, $key) . ': ';
			if (is_array($value)) {
				$output .= self::outputDiffTypeData($entity, $value, $output);
			} elseif (is_bool($value)) {
				$output .= $value ? 'Да' : 'Нет';
			} else {
				$output .= $value ?? '-';
			}
			$output .= '<br>';
		}

		return $output;
	}
	
	/**
	 * @param $array
	 * @return bool
	 */
	public static function isAssoc($array)
	{
		foreach (array_keys($array) as $k => $v) {
			if ($k !== $v) return true;
		}
		return false;
	}
	
	/**
	 * @return array
	 */
	public static function getStatusesByType()
	{
		$statuses = Status::where('is_active', true)
			->get();
		
		$statusesData = [];
		
		foreach ($statuses ?? [] as $status) {
			$data = $status->data_json;
			$statusesData[$status->type][$status->alias] = [
				'id' => $status->id,
				'name' => $status->name,
				'sort' => $status->sort,
				'flight_time' => ($data && array_key_exists('flight_time', $data)) ? $data['flight_time'] : null,
				'discount' => ($data && array_key_exists('discount', $data)) ? $data['discount'] : null,
			];
		}
		
		return $statusesData;
	}
	
	/**
	 * @param $alias
	 * @return City|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
	 */
	public static function getCityByAlias($alias)
	{
		return City::where('alias', $alias)
			->first();
	}
	
	/**
	 * @param $alias
	 * @return LegalEntity|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
	 */
	public static function getLegalEntityByAlias($alias)
	{
		return LegalEntity::where('alias', $alias)
			->first();
	}
	
	/**
	 * @param $alias
	 * @return LegalEntity|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
	 */
	public static function getStatusByAlias($alias)
	{
		return Status::where('alias', $alias)
			->first();
	}
	
	/**
	 * @param $alias
	 * @return ProductType|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
	 */
	public static function getProductTypeByAlias($alias)
	{
		return ProductType::where('alias', $alias)
			->first();
	}
	
	/**
	 * @param $flightTime
	 * @return Status|Status[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
	 */
	public static function getContractorStatus($flightTime)
	{
		$statuses = Status::where('is_active', true)
			->where('type', 'contractor')
			->get();
		if ($statuses->isEmpty()) return null;
		
		$flightTimes = [];
		foreach ($statuses as $status) {
			$data = $status->data_json ?? [];
			$flightTimes[$status->id] = array_key_exists('flight_time', $data) ? intval($data['flight_time']) : 0;
		}
		
		krsort($flightTimes);
		$result = array_filter($flightTimes, function($item) use ($flightTime) {
			return $item <= $flightTime;
		});
		if (!$result) return null;
		
		$statusId = array_key_first($result);
		
		return Status::find($statusId);
	}
	
	/**
	 * @param $authToken
	 * @return mixed
	 */
	public static function validToken($authToken)
	{
		$date = date('Y-m-d H:i:s');
		
		return Token::where('token', $authToken)
			->where(function ($query) use ($date) {
				$query->where('expire_at', '>=', $date)
					->orWhereNull('expire_at');
			})
			->first();
	}
	
	/**
	 * @return int
	 */
	public static function getNewOrderCount()
	{
		return Order::whereHas('status', function ($query) {
			$query->where('type', Status::STATUS_TYPE_ORDER)
				->where('alias', Order::RECEIVED_STATUS);
		})->count();
	}
	
	/**
	 * @return int
	 */
	public static function getNewDealCount()
	{
		return DealPosition::whereHas('status', function ($query) {
			$query->where('type', Status::STATUS_TYPE_DEAL)
				->where('alias', DealPosition::CREATED_STATUS);
		})->count();
	}
	
	/**
	 * @return int
	 */
	public static function getNewBillCount()
	{
		return Bill::whereHas('status', function ($query) {
			$query->where('type', Status::STATUS_TYPE_BILL)
				->where('alias', Bill::NOT_PAYED_STATUS);
		})->count();
	}

	/**
	 * @return int
	 */
	public static function getNewPaymentCount()
	{
		return Payment::whereHas('status', function ($query) {
			$query->where('type', Status::STATUS_TYPE_PAYMENT)
				->where('alias', Payment::NOT_SUCCEED_STATUS);
		})->count();
	}
}
