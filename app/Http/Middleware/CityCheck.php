<?php

namespace App\Http\Middleware;

use App\Models\City;
use Closure;
use Illuminate\Http\Request;

class CityCheck
{
	public function handle(Request $request, Closure $next)
	{
		$cityAlias = $request->session()->get('cityAlias');
		
		$cityAliases = City::where('is_active', true)
			->pluck('alias')->all();

		// если в урл не указан город
		if (!in_array($request->segment(1), $cityAliases)) {
			// если есть сессия
			if ($cityAlias) {
				return redirect($cityAlias . '/' . $request->segment(1) . '/');
			}
			// по умолчанию Москва
			return redirect(City::MSK_ALIAS . '/' . $request->segment(1) . '/');
		}
		
		return $next($request);
	}
}