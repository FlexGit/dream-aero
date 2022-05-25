<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\City;
use App\Models\Deal;
//use App\Models\Payment;

use App\Services\PayAnyWayService;

class PayController extends Controller {
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
}