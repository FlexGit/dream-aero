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
	 * @param $entity
	 * @param $uuid
	 *
	 * @return mixed
	 */
	public static function getEntityByUuid($entity, $uuid)
	{
		return app($entity)::where('uuid', $uuid)
			->first();
	}
	
	/**
	 * @param $entity
	 * @param $number
	 * @return mixed
	 */
	public static function getEntityByNumber($entity, $number)
	{
		return app($entity)::where('number', $number)
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
	
	/**
	 * @param $md5
	 * @return false|int
	 */
	public static function isValidMd5($md5)
	{
		return preg_match('/^[a-f0-9]{32}$/', $md5);
	}
	
	/**
	 * @param $structure
	 * @return array
	 */
	public static function mailSearch($structure)
	{
		if ($structure->subtype == 'HTML' || $structure->type == 0) {
			if ($structure->parameters[0]->attribute == "charset") {
				$charset = $structure->parameters[0]->value;
			}
			
			return [
				'encoding' => (int)$structure->encoding,
				'charset' => strtolower($charset),
				'subtype' => $structure->subtype
			];
		}

		if (isset($structure->parts[0])) {
			return self::mailSearch($structure->parts[0]);
		}

		if ($structure->parameters[0]->attribute == 'charset') {
			$charset = $structure->parameters[0]->value;
		}
		
		return [
			'encoding' => (int)$structure->encoding,
			'charset'  => strtolower($charset),
			'subtype'  => $structure->subtype
		];
	}
	
	/**
	 * @param $encoding
	 * @param $msgBody
	 * @return string
	 */
	public static function mailStructureEncoding($encoding, $msgBody)
	{
		switch ($encoding) {
			case 4:
				$body = imap_qprint($msgBody);
			break;
			case 3:
				$body = imap_base64($msgBody);
			break;
			case 2:
				$body = imap_binary($msgBody);
			break;
			case 1:
				$body = imap_8bit($msgBody);
			break;
			case 0:
				$body = $msgBody;
			break;
			default:
				$body = '';
			break;
		}
		
		return $body;
	}
	
	/**
	 * @param $charset
	 * @return bool
	 */
	public static function mailCheckUtf8($charset)
	{
		if (strtolower($charset) != 'utf-8') return false;
		return true;
	}
	
	/**
	 * @param $inCharset
	 * @param $str
	 * @return bool|false|string
	 */
	public static function mailConvertToUtf8($inCharset, $str)
	{
		return iconv(strtolower($inCharset), 'utf-8', $str);
	}
	
	/**
	 * @param $string
	 * @param $start
	 * @param $length
	 * @return bool|string
	 */
	public static function mailGetStringBefore($string, $start, $length)
	{
		$pos = strpos($string, $start);
		if ($pos == 0) return '';
		
		$pos = $pos - ($length + 1);
		return substr($string, $pos, $length);
	}
	
	/**
	 * @param $string
	 * @param $start
	 * @param $end
	 * @return bool|string
	 */
	public static function mailGetStringBetween($string, $start, $end)
	{
		$string = ' ' . $string;
		$pos = strpos($string, $start);
		if ($pos == 0) return '';
		
		$pos += strlen($start);
		if ($end != '') {
			$len = strpos($string, $end, $pos) - $pos;
			
			return substr($string, $pos, $len);
		}

		return substr($string, $pos);
	}
	
	/**
	 * @param $str
	 * @return mixed
	 */
	public static function mailGetImapTitle($str)
	{
		$mime = imap_mime_header_decode($str);
		$title = '';
		foreach ($mime as $key => $m) {
			if (!self::mailCheckUtf8($m->charset)) {
				$title .= self::mailConvertToUtf8($m->charset, $m->text);
			} else {
				$title .= $m->text;
			}
		}
		
		return $m->text;
	}
	
	/**
	 * @param $time
	 * @return float|int
	 */
	public static function mailGetTimeSeconds($time)
	{
		$timeParts = explode(':', $time);
		return $timeParts[0] * 3600 + $timeParts[1] * 60 + $timeParts[2];
	}
	
}
