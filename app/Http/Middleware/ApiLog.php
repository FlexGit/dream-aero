<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApiLog
{
	public function handle(Request $request, Closure $next)
	{
		Log::info('Incoming request:');
		Log::channel('api')->info($request);
		
		return $next($request);
	}
	
	public function terminate(Request $request, \Illuminate\Support\Facades\Response $response)
	{
		Log::info('Outgoing response:');
		Log::info($response);
	}
}