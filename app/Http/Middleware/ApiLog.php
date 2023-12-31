<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiLog
{
	public function handle(Request $request, Closure $next)
	{
		Log::channel('api')->info($_SERVER['REMOTE_ADDR']);
		Log::channel('api')->info($request);

		return $next($request);
	}
}