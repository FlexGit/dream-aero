<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

use App\Models\Order;
use App\Models\Deal;
use App\Models\DealPosition;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Status;
use App\Models\Contractor;

class DealController extends Controller
{
	private $request;
	
	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$cities = City::orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();
		
		/*$products = Product::orderBy('name')
			->get();*/
		
		$productTypes = ProductType::orderBy('name')
			->get();

		$statuses = Status::where('type', Status::STATUS_TYPE_DEAL)
			->orderBy('sort')
			->get();

		return view('admin.deal.index', [
			'cities' => $cities,
			'locations' => $locations,
			/*'products' => $products,*/
			'productTypes' => $productTypes,
			'statuses' => $statuses,
		]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getListAjax()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$deals = Deal::with(['contractor', 'status'])
			->orderBy('id', 'desc');
		if ($this->request->filter_status_id) {
			$deals = $deals->where('status_id', $this->request->filter_status_id);
		}
		/*if ($this->request->filter_city_id) {
			$deals = $deals->where('city_id', $this->request->filter_city_id);
		}
		if ($this->request->filter_location_id) {
			$orders = $orders->where('location_id', $this->request->filter_location_id);
		}*/
		if ($this->request->filter_contractor_id) {
			$deals = $deals->where('contractor_id', $this->request->filter_contractor_id);
		}
		$deals = $deals->get();
		
		$VIEW = view('admin.deal.list', ['deals' => $deals]);

		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
	 */
	public function edit($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$deal = Deal::find($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$cities = City::orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();
		
		$productTypes = ProductType::with(['products'])
			->orderBy('name')
			->get();
		
		$statuses = Status::orderBy('sort')
			->get();

		$VIEW = view('admin.deal.modal.edit', [
			'deal' => $deal,
			'cities' => $cities,
			'locations' => $locations,
			'productTypes' => $productTypes,
			'statuses' => $statuses,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id)
	{
		$deal = Deal::find($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$cities = City::orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();
		
		$products = Product::orderBy('name')
			->get();
		
		$statuses = Status::orderBy('sort')
			->get();
		
		$VIEW = view('admin.deal.modal.show', [
			'deal' => $deal,
			'cities' => $cities,
			'locations' => $locations,
			'products' => $products,
			'statuses' => $statuses,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function add()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}
		
		$cities = City::where('is_active', true)
			->orderBy('name')
			->get();
		
		$locations = Location::where('is_active', true)
			->orderBy('name')
			->get();
		
		$productTypes = ProductType::where('is_active', true)
			->with(['products'])
			->orderBy('name')
			->get();
		
		$VIEW = view('admin.deal.modal.add', [
			'cities' => $cities,
			'locations' => $locations,
			'productTypes' => $productTypes,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$rules = [
			'status_id' => 'required|numeric',
			'product_id' => 'required|numeric',
			'city_id' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'status_id' => 'Статус',
				'product_id' => 'Продукт',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$deal = new Deal();
		$deal->number = $this->request->number;
		$deal->status_id = $this->request->status_id;
		$deal->product_id = $this->request->product_id;
		$deal->city_id = $this->request->city_id;
		if (!$deal->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $deal->id]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$deal = Deal::find($id);
		if (!$deal) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$rules = [
			'status_id' => 'required|numeric',
			'product_id' => 'required|numeric',
			'city_id' => 'required|numeric',
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'status_id' => 'Статус',
				'product_id' => 'Продукт',
				'city_id' => 'Город',
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		$deal->number = $this->request->number;
		$deal->status_id = $this->request->status_id;
		$deal->product_id = $this->request->product_id;
		$deal->city_id = $this->request->city_id;

		if (!$deal->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $deal->id]);
	}
}
