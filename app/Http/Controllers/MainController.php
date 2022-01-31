<?php

namespace App\Http\Controllers;

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
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias ?: City::MSK_ALIAS);
		
		$users = User::where('enable', true)
			->whereIn('city_id', [$city->id, 0])
			->whereIn('role', [User::ROLE_ADMIN, User::ROLE_PILOT])
			->orderBy('name')
			->get();
		
		$reviews = Review::where('is_active', true)
			->whereIn('city_id', [$city->id, 0])
			->latest()
			->limit(10)
			->get();
		
		return view('home', [
			'users' => $users,
			'reviews' => $reviews,
			'city' => $city,
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
		$city = HelpFunctions::getEntityByAlias(City::class, $cityAlias);
		
		$productTypes = ProductType::where('is_active', true)
			->orderBy('name')
			->get();

		$products = Product::where('is_active', true)
			->where('city_id', $city->id)
			->orderBy('name')
			->get();

		return view('price', [
			'productTypes' => $productTypes,
			'products' => $products,
			'city' => $city,
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