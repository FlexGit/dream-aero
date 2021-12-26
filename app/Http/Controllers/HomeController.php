<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Order;
use App\Models\Employee;
use App\Models\Review;
use App\Models\Location;
use App\Models\FlightSimulatorType;
use App\Models\ProductType;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
	private $request;
	private $user;

	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->user = Auth::user();
		$this->request = $request;
	}

	/**
	 * "Домашняя" страница
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function home()
	{
		return view('admin/home', [
		]);
	}
	
	public function clear()
	{
		Order::where('user_id', 0)
			->delete();
		Deal::where('user_id', 0)
			->delete();
	}
}
