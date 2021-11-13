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
		return response()->json([
			'success' => false,
			'error' => $error,
			'debug' => $debug,
		], $code);
	}
	
	/*protected function responseErrors($errors = [], $code = 200, $debug = null)
	{
		return response()->json([
			'success' => false,
			'error' => $errors,
			'debug' => $debug,
		], $code);
	}*/
}
