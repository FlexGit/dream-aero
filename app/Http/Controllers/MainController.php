<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\HelpFunctions;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Review;
use App\Models\Location;
use App\Models\FlightSimulator;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\User;

class MainController extends Controller
{
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function home($cityAlias = null)
	{
		//dump($cityAlias);exit;
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);

		$users = User::where('enable', true)
			->whereIn('city_id', [$city->id, 0])
			->whereIn('role', [User::ROLE_ADMIN, User::ROLE_PILOT])
			->orderBy('name')
			->get();
		
		$reviews = Review::where('is_active', true)
			/*->whereIn('city_id', [$city->id, 0])*/
			->latest()
			->limit(10)
			->get();
		
		return view('home', [
			'users' => $users,
			'reviews' => $reviews,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function about($cityAlias = null)
	{
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias);
		
		$flightSimulatorTypes = FlightSimulator::get();

		$locations = Location::where('is_active', true)
			->where('city_id', $city->id)
			->orderBy('name')
			->get();

		return view('about', [
			'flightSimulatorTypes' => $flightSimulatorTypes,
			'locations' => $locations,
			'city' => $city,
		]);
	}
	
	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function virtualTour($cityAlias = null)
	{
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias);
		
		return view('virtual-tour', [
			'city' => $city,
		]);
	}
	
	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function contacts($cityAlias = null)
	{
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias);
		
		$locations = Location::where('is_active', true)
			->where('city_id', $city->id)
			->orderBy('name')
			->with('simulator')
			->get();

		return view('contacts', [
			'locations' => $locations,
			'city' => $city,
		]);
	}
	
	/**
	 * @param null $cityAlias
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function price($cityAlias = null)
	{
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$productTypes = ProductType::where('is_active', true)
			->where('version', $city->version)
			->orderBy('name')
			->get();
		
		$cityProducts = $city->products;
		
		$products = [];
		foreach ($productTypes as $productType) {
			$products[mb_strtoupper($productType->alias)] = [];
			
			foreach ($productType->products ?? [] as $product) {
				foreach ($cityProducts ?? [] as $cityProduct) {
					if ($product->id != $cityProduct->id) continue;
					
					$price = $cityProduct->pivot->price;
					if ($cityProduct->pivot->discount) {
						$price = $cityProduct->pivot->discount->is_fixed ? ($price - $cityProduct->pivot->discount->value) : ($price - $price * $cityProduct->pivot->discount->value / 100);
					}
					
					$pivotData = json_decode($cityProduct->pivot->data_json, true);
					
					$products[mb_strtoupper($productType->alias)][$product->alias] = [
						'id' => $product->id,
						'name' => $product->name,
						'alias' => $product->alias,
						'duration' => $product->duration,
						'price' => round($price),
						'currency' => $cityProduct->pivot->currency ? $cityProduct->pivot->currency->name : 'руб',
						'is_hit' => (bool)$cityProduct->pivot->is_hit,
						'is_booking_allow' => false,
						'is_certificate_purchase_allow' => false,
						'icon' => (is_array($product->data_json) && array_key_exists('icon', $product->data_json)) ? $product->data_json['icon'] : '',
					];
					
					if (array_key_exists('is_booking_allow', $pivotData) && $pivotData['is_booking_allow']) {
						$products[mb_strtoupper($productType->alias)][$product->alias]['is_booking_allow'] = true;
					}
					if (array_key_exists('is_certificate_purchase_allow', $pivotData) && $pivotData['is_certificate_purchase_allow']) {
						$products[mb_strtoupper($productType->alias)][$product->alias]['is_certificate_purchase_allow'] = true;
					}
				}
			}
		}
		
		//dump($products);exit;
		
		return view('price', [
			'productTypes' => $productTypes,
			'products' => $products,
			'city' => $city,
			'cityAlias' => $cityAlias,
		]);
	}
	
	public function getCityListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cityAlias = $this->request->session()->get('cityAlias', City::MSK_ALIAS);
		
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias);

		$cities = City::where('is_active', true)
			->get();
		
		$VIEW = view('city.list', [
			'cities' => $cities,
			'city' => $city,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	public function changeCity()
	{
		$cityAlias = $this->request->alias ?? '';

		$this->request->session()->put('cityAlias', $cityAlias);
		
		return response()->json(['status' => 'success', 'cityAlias' => $cityAlias]);
	}
}