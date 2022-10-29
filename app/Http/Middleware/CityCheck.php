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
		if ($_SERVER['REMOTE_ADDR'] == '79.165.99.239') {
			\Log::debug('label 1: ' . $request->session()->get('cityAlias') . ' - ' . $request->segment(1));
		}
		
		if ($request->ajax()) return $next($request);
		
		$cityAliases = ($request->session()->get('cityVersion') == City::EN_VERSION) ? City::EN_ALIASES : City::RU_ALIASES;
		
		if (in_array($request->segment(1), $cityAliases)
			&& $request->session()->get('cityAlias')
			&& ($request->segment(1) != $request->session()->get('cityAlias'))
		) {
			$city = HelpFunctions::getEntityByAlias(City::class, $request->segment(1));
			if ($city) {
				$cityName = \App::isLocale('en') ? $city->name_en : $city->name;
				
				$request->session()->put('cityId', $city->id);
				$request->session()->put('cityAlias', $city->alias);
				$request->session()->put('cityVersion', $city->version);
				$request->session()->put('cityName', $cityName);
				$request->session()->put('isCityConfirmed', false);
				
				return $next($request);
			}
		}
		
		//\Log::debug('label 1.0: ' . $_SERVER['REMOTE_ADDR']);
		if ($_SERVER['REMOTE_ADDR'] == '79.165.99.239') {
			\Log::debug('label 1.1: ' . $request->segment(1));
		}

		if (in_array($request->segment(1), ['', 'contacts', 'price'])) {
			if ($request->session()->get('cityAlias')) {
				return redirect(($request->session()->get('cityAlias') ?? City::MSK_ALIAS) . ($request->segment(1) ? '/' . $request->segment(1) : ''), 301);
			}

			$ipData = geoip()->getLocation(geoip()->getClientIP())->toArray();
			
			if ($_SERVER['REMOTE_ADDR'] == '79.165.99.239') {
				\Log::debug($ipData);
			}

			if (isset(City::GEO_ALIASES[$ipData['state']])) {
				$city = HelpFunctions::getEntityByAlias(City::class, City::GEO_ALIASES[$ipData['state']]);
				if ($city) {
					$cityName = \App::isLocale('en') ? $city->name_en : $city->name;
					
					$request->session()->put('cityId', $city->id);
					$request->session()->put('cityAlias', $city->alias);
					$request->session()->put('cityVersion', $city->version);
					$request->session()->put('cityName', $cityName);
					$request->session()->put('isCityConfirmed', false);
					
					if ($_SERVER['REMOTE_ADDR'] == '79.165.99.239') {
						\Log::debug('label 2: ' . $request->session()->get('cityAlias'));
					}
					
					return redirect(($request->session()->get('cityAlias') ?? City::MSK_ALIAS) . ($request->segment(1) ? '/' . $request->segment(1) : ''), 301);
				}
			}
			
			if ($_SERVER['REMOTE_ADDR'] == '79.165.99.239') {
				\Log::debug('label 3: ' . $request->session()->get('cityAlias'));
			}
			return redirect(City::MSK_ALIAS . ($request->segment(1) ? '/' . $request->segment(1) : ''), 301);
		}
		
		if ($_SERVER['REMOTE_ADDR'] == '79.165.99.239') {
			\Log::debug('label 4: ' . $request->session()->get('cityAlias'));
		}
		return $next($request);
	}
}