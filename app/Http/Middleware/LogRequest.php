<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogRequest
{
	public function handle(Request $request, Closure $next)
	{
		\Log::channel('api')->debug($request);
		
		return $next($request);
	}
}