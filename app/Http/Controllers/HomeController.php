<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Employee;
use App\Models\Review;
use App\Models\Location;
use App\Models\FlightSimulatorType;
use App\Models\File;
use App\Models\TariffType;
use App\Models\Tariff;

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
}
