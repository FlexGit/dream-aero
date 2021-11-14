<?php

namespace App\Traits;

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
		], 200);
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
				default:
					$error = 'Неизвестная ошибка';
				break;
			}
		}
		
		return response()->json([
			'success' => false,
			'error' => $error,
			'debug' => $debug,
		], $code);
	}
}
