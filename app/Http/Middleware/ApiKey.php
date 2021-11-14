<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponser;

class ApiKey
{
	use ApiResponser;
	
	public function handle($request, Closure $next)
	{
		$apiKey = $request->api_key ?? '';
		if (!$apiKey || $apiKey !== config('app.api_key')) {
			return $this->responseError('Некорректный Api-ключ', 400);
		}
		
		return $next($request);
	}
}