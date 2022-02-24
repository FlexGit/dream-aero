<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ApiResponser
{
	/**
	 * @param null $message
	 * @param array $data
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseSuccess($message = null, $data = null)
	{
		if ($message) {
			Log::channel('api')->info($message);
		}
		if ($data) {
			Log::channel('api')->info($data);
		}
		
		return response()->json([
			'success' => true,
			'message' => $message,
			'data' => $data,
		], 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * @param null $error
	 * @param int $code
	 * @param null $debug
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function responseError($error = null, $code = 200, $debug = null)
	{
		if (!$error) {
			switch ($code) {
				case 500:
					$error = 'Внутренняя ошибка';
				break;
				case 405:
					$error = 'Метод не разрешен';
				break;
				case 404:
					$error = 'Ресурс не найден';
				break;
				case 400:
					$error = 'Некорректный запрос';
				break;
				case 429:
					$error = 'Слишком много попыток. Попробуйте позже';
				break;
				default:
					$error = 'Неизвестная ошибка';
				break;
			}
		}
		
		if ($error) {
			Log::channel('api')->info($error);
		}
		if ($debug) {
			Log::channel('api')->info($debug);
		}

		return response()->json([
			'success' => false,
			'error' => $error,
			'debug' => $debug,
		], $code, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
	}
}
