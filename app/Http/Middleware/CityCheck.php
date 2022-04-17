<?php

namespace App\Http\Middleware;

use App\Models\City;
use App\Services\HelpFunctions;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CityCheck
{
	public function handle(Request $request, Closure $next)
	{
		/*if (!$request->session()->get('cityAlias')) {
			$city = HelpFunctions::getEntityByAlias(City::class, City::MSK_ALIAS);

			$cityName = \App::isLocale('en') ? $city->name_en : $city->name;
		
			$request->session()->put('cityId', $city->id);
			$request->session()->put('cityAlias', $city->alias);
			$request->session()->put('cityVersion', $city->version);
			$request->session()->put('cityName', $cityName);
		}*/
		
		if ($request->ajax()) return $next($request);
		
		//dump($request->session()->get('cityAlias') . ' - ' . $request->segment(1) . ' - ' . $request->segment(2));

		$cityAliases = ($request->session()->get('cityVersion') == City::EN_VERSION) ? City::EN_ALIASES : City::RU_ALIASES;
		
		if (in_array($request->segment(1), $cityAliases) && $request->session()->get('cityAlias') && ($request->segment(1) != $request->session()->get('cityAlias'))) {
			return redirect($request->session()->get('cityAlias') . ($request->segment(1) ? '/' . $request->segment(2) : ''));
		}

		if (in_array($request->segment(1), ['', 'contacts', 'price'])) {
			return redirect(($request->session()->get('cityAlias') ?? City::MSK_ALIAS) . ($request->segment(1) ? '/' . $request->segment(1) : ''), 301);
		}
		
		/*if ($request->segment(2) && !in_array($request->segment(1), $cityAliases)) {
			abort(404);
		}*/

		/*if (!Route::has($request->segment(1)) && !in_array($request->segment(1), $cityAliases)) {
			abort(404);
		}*/
		
		return $next($request);
	}
}