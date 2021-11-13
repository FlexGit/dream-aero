<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use App\Traits\ApiResponser;

class ApiKey
{
	use ApiResponser;
	
	private $request;
	
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function handle($request, Closure $next)
	{
		if (!$this->request->apikey || $this->request->apikey !== config('app.api_key')) {
			return $this->responseError('Некорректный Api-ключ');
		}
		
		return $next($request);
	}
}