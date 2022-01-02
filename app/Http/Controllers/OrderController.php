<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Order;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Status;
use App\Models\Contractor;

class OrderController extends Controller
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
		
		$statuses = Status::where('type', Status::STATUS_TYPE_ORDER)
			->orderBy('sort')
			->get();
		
		return view('admin.order.index', [
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
		
		$id = $this->request->id ?? 0;
		
		$orders = Order::with(['city', 'location', 'product', 'contractor', 'status'])
			->orderBy('id', 'desc');
		if ($id) {
			$orders = $orders->where('id', '<', $id);
		}
		if ($this->request->filter_status_id) {
			$orders = $orders->where('status_id', $this->request->filter_status_id);
		}
		if ($this->request->filter_city_id) {
			$orders = $orders->where('city_id', $this->request->filter_city_id);
		}
		if ($this->request->filter_location_id) {
			$orders = $orders->where('location_id', $this->request->filter_location_id);
		}
		if ($this->request->filter_product_type_id) {
			$orders = $orders->where(function ($query) {
				$query->whereHas('product', function ($q) {
					return $q->where('product_type_id', '=', $this->request->filter_product_type_id);
				});
			});
		}
		if ($this->request->search_doc) {
			$orders = $orders->where(function ($query) {
				$query->where('number', 'like', '%' . $this->request->search_doc . '%')
					->orWhereHas('certificate', function ($q) {
						return $q->where('number', 'like', '%' . $this->request->search_doc . '%');
					})
					->orWhereHas('dealPosition', function ($q) {
						return $q->where('number', 'like', '%' . $this->request->search_doc . '%');
					})
					->orWhereHas('bill', function ($q) {
						return $q->where('number', 'like', '%' . $this->request->search_doc . '%');
					});
			});
		}
		if ($this->request->search_contractor) {
			$orders = $orders->whereHas('contractor', function ($query) {
				return $query->where('name', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('lastname', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('email', 'like', '%' . $this->request->search_contractor . '%')
					->orWhere('phone', 'like', '%' . $this->request->search_contractor . '%');
			});
		}
		$orders = $orders->limit(10)->get();
		
		$VIEW = view('admin.order.list', ['orders' => $orders]);

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

		$order = Order::find($id);
		if (!$order) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		/*$cities = City::orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();
		
		$productTypes = ProductType::with(['products'])
			->orderBy('name')
			->get();*/

		$statuses = Status::where('type', Status::STATUS_TYPE_ORDER)
			->orderBy('sort')
			->get();

		$VIEW = view('admin.order.modal.edit', [
			'order' => $order,
			/*'cities' => $cities,
			'locations' => $locations,
			'productTypes' => $productTypes,*/
			'statuses' => $statuses,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	/*public function show($id)
	{
		$order = Order::find($id);
		if (!$order) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$cities = City::orderBy('name')
			->get();
		
		$locations = Location::orderBy('name')
			->get();
		
		$products = Product::orderBy('name')
			->get();
		
		$statuses = Status::orderBy('sort')
			->get();
		
		$VIEW = view('admin.order.modal.show', [
			'order' => $order,
			'cities' => $cities,
			'locations' => $locations,
			'products' => $products,
			'statuses' => $statuses,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}*/
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	/*public function add()
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
		
		$VIEW = view('admin.order.modal.add', [
			'cities' => $cities,
			'locations' => $locations,
			'productTypes' => $productTypes,
		]);
		
		return response()->json(['status' => 'success', 'html' => (string)$VIEW]);
	}*/
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	/*public function store()
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
		
		$order = new Order();
		$order->number = $this->request->number;
		$order->status_id = $this->request->status_id;
		$order->product_id = $this->request->product_id;
		$order->city_id = $this->request->city_id;
		if (!$order->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $order->id]);
	}*/
	
	/**
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
		if (!$this->request->ajax()) {
			abort(404);
		}

		$order = Order::find($id);
		if (!$order) return response()->json(['status' => 'error', 'reason' => 'Нет данных']);
		
		$rules = [
			'status_id' => 'required|numeric',
			/*'product_id' => 'required|numeric',
			'city_id' => 'required|numeric',*/
		];
		
		$validator = Validator::make($this->request->all(), $rules)
			->setAttributeNames([
				'status_id' => 'Статус',
				/*'product_id' => 'Продукт',
				'city_id' => 'Город',*/
			]);
		if (!$validator->passes()) {
			return response()->json(['status' => 'error', 'reason' => $validator->errors()->all()]);
		}
		
		/*$order->number = $this->request->number;*/
		$order->status_id = $this->request->status_id;
		/*$order->product_id = $this->request->product_id;
		$order->city_id = $this->request->city_id;*/

		if (!$order->save()) {
			return response()->json(['status' => 'error', 'reason' => 'В данный момент невозможно выполнить операцию, повторите попытку позже!']);
		}
		
		return response()->json(['status' => 'success', 'id' => $order->id]);
	}
}
