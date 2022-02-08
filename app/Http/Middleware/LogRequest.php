<?php

namespace App\Http\Middleware;

use Closure;

class LogRequest
{
	public function handle($request, Closure $next)
	{
		\Log::channel('api')->debug($request);
		
		return $next($request);
	}
}