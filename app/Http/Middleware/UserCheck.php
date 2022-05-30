<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserCheck
{
	public function handle(Request $request, Closure $next)
	{
		$user = $request->user();
		if (!$user->enable){
			auth()->logout();
			return redirect('/login');
		}
		
		return $next($request);
	}
}