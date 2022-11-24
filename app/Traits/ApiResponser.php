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

		Log::channel('api')->error($_SERVER['REMOTE_ADDR']);
		if ($error) {
			Log::channel('api')->error($error);
		}
		if ($debug) {
			Log::channel('api')->debug($debug);
		}

		return response()->json([
			'success' => false,
			'error' => $error,
			'debug' => $debug,
		], $code, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
	}
}
