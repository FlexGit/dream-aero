<?php

namespace App\Services;

use App\Models\Deal;
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
	 * @param $entity
	 * @param $alias
	 * @return mixed
	 */
	public static function getEntityByAlias($entity, $alias)
	{
		return app($entity)::where('alias', $alias)
			->first();
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
	/*public static function getNewOrderCount()
	{
		return Order::whereHas('status', function ($query) {
			$query->where('type', Status::STATUS_TYPE_ORDER)
				->where('alias', Order::RECEIVED_STATUS);
		})->count();
	}*/
	
	/**
	 * @return int
	 */
	public static function getNewDealCount()
	{
		return Deal::whereHas('status', function ($query) {
			$query->where('type', Status::STATUS_TYPE_DEAL)
				->where('alias', Deal::CREATED_STATUS);
		})->count();
	}
	
	/**
	 * @return int
	 */
	/*public static function getNewBillCount()
	{
		return Bill::whereHas('status', function ($query) {
			$query->where('type', Status::STATUS_TYPE_BILL)
				->where('alias', Bill::NOT_PAYED_STATUS);
		})->count();
	}*/

	/**
	 * @return int
	 */
	/*public static function getNewPaymentCount()
	{
		return Payment::whereHas('status', function ($query) {
			$query->where('type', Status::STATUS_TYPE_PAYMENT)
				->where('alias', Payment::NOT_SUCCEED_STATUS);
		})->count();
	}*/
	
	public static function formatPhone($phone)
	{
		$phoneFormated = /*substr($phone, 0, 2) . ' (' . substr($phone, 2, 3) . ') ' . substr($phone, 5, 3) . '-' . substr($phone, 8, 2) . '-' . substr($phone, 10)*/$phone;

		return $phoneFormated ?? '';
	}
}
