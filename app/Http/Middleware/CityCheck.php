<?php

namespace App\Http\Middleware;

use App\Models\City;
use App\Services\HelpFunctions;
use Closure;
use Illuminate\Http\Request;

class CityCheck
{
	public function handle(Request $request, Closure $next)
	{
		if ($request->ajax()) return $next($request);
		
		$cityAliases = ($request->session()->get('cityVersion') == City::EN_VERSION) ? City::EN_ALIASES : City::RU_ALIASES;

		// цены, контакты или главная
		if (in_array($request->segment(1), $cityAliases) || in_array($request->segment(1), ['price', 'contacts'])) {
			// есть сессия и перешли в другой город
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
					$request->session()->put('isCityConfirmed', true);
					
					return $next($request);
				}
			} elseif (in_array($request->segment(1), ['price', 'contacts'])) { // страница цен или контактов без города
				// есть есть сессия, редирект на страницу цен или контактов города по сессии
				if ($request->session()->get('cityAlias')) {
					return redirect($request->session()->get('cityAlias') . ($request->segment(1) ? '/' . $request->segment(1) : ''), 301);
				}
				
				// определяем город по IP
				$ipData = geoip()->getLocation(geoip()->getClientIP())->toArray();
				// если нашли город по IP, пишем его в сессию и редиректим на него
				if (isset(City::GEO_ALIASES[$ipData['state']])) {
					$city = HelpFunctions::getEntityByAlias(City::class, City::GEO_ALIASES[$ipData['state']]);
					if ($city) {
						$cityName = \App::isLocale('en') ? $city->name_en : $city->name;
						
						$request->session()->put('cityId', $city->id);
						$request->session()->put('cityAlias', $city->alias);
						$request->session()->put('cityVersion', $city->version);
						$request->session()->put('cityName', $cityName);
						$request->session()->put('isCityConfirmed', false);
						
						return redirect($request->session()->get('cityAlias') . ($request->segment(1) ? '/' . $request->segment(1) : ''), 301);
					}
				}
				
				// если город не нашли, редиректим на Москву
				return redirect(City::MSK_ALIAS . ($request->segment(1) ? '/' . $request->segment(1) : ''), 301);
			}
		} elseif (in_array($request->segment(2), ['price', 'contacts']) && !in_array($request->segment(1), $cityAliases)) { // страница цен или контактов, но с некорректным алиасом города
			abort(404);
		} elseif (!$request->segment(1)) { // главная страница
			// если есть сессия города, редиректим на него
			if ($request->session()->get('cityAlias')) {
				return redirect($request->session()->get('cityAlias'), 301);
			}
			
			// определяем город по IP
			$ipData = geoip()->getLocation(geoip()->getClientIP())->toArray();
			// если нашли город по IP, пишем его в сессию и редиректим на него
			if (isset(City::GEO_ALIASES[$ipData['state']])) {
				$city = HelpFunctions::getEntityByAlias(City::class, City::GEO_ALIASES[$ipData['state']]);
				if ($city) {
					$cityName = \App::isLocale('en') ? $city->name_en : $city->name;
					
					$request->session()->put('cityId', $city->id);
					$request->session()->put('cityAlias', $city->alias);
					$request->session()->put('cityVersion', $city->version);
					$request->session()->put('cityName', $cityName);
					$request->session()->put('isCityConfirmed', false);
					
					return redirect($request->session()->get('cityAlias'), 301);
				}
			}
			
			// если город не нашли, редиректим на Москву
			return redirect(City::MSK_ALIAS, 301);
		}
		
		return $next($request);
	}
}