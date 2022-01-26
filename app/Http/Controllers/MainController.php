<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Review;
use App\Models\Location;
use App\Models\FlightSimulator;
use App\Models\ProductType;
use App\Models\Product;

class MainController extends Controller
{
	private $cityId;

	public function __construct()
	{
		// ToDo: брать из сессии
		$this->cityId = 1;
	}

	/**
	 * "Домашняя" страница
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function home()
	{
		$employees = Employee::where('is_active', true)
			->whereHas('location', function ($q) {
				return $q->where('city_id', $this->cityId);
			})
			->orderBy('name')
			->get();

		$reviews = Review::where('is_active', true)
			->latest()
			->limit(10)
			->get();

		return view('home', [
			'employees' => $employees,
			'reviews' => $reviews,
		]);
	}

	/**
	 * Страница "О тренажере"
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function about()
	{
		$flightSimulatorTypes = FlightSimulator::get();

		$locations = Location::where('is_active', true)
			->where('city_id', $this->cityId)
			->orderBy('name')
			->get();

		return view('about', [
			'flightSimulatorTypes' => $flightSimulatorTypes,
			'locations' => $locations,
		]);
	}

	/**
	 * Раздел "Виртуальный тур"
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function virtualTour()
	{
		return view('virtual-tour');
	}

	/**
	 * Страница "Контакты"
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function contacts()
	{
		$locations = Location::where('is_active', true)
			->where('city_id', $this->cityId)
			->orderBy('name')
			->with('simulator')
			->get();

		return view('contacts', [
			'locations' => $locations,
		]);
	}

	public function price()
	{
		$productTypes = ProductType::where('is_active', true)
			->orderBy('name')
			->get();

		$products = Product::where('is_active', true)
			->where('city_id', $this->cityId)
			->orderBy('name')
			->get();

		return view('price', [
			'productTypes' => $productTypes,
			'products' => $products,
		]);
	}
}